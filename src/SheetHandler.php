<?php

namespace Twelver313\Sheetmap;

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Twelver313\Sheetmap\MetadataResolver;
use Twelver313\Sheetmap\Validation\SheetValidationContext;
use Twelver313\Sheetmap\Validation\SheetValidationPipeline;

class SheetHandler
{
  /** @var MetadataResolver */
  private $metadataResolver;
  /** @var Worksheet */
  private $sheet;
  /** @var ValueFormatter */
  private $valueFormatter;
  /** @var MappingProvider */
  private $mapping;
  private $startRow = 2;
  private $endRow = null;
  private $sheetHeader = [];
  private $errors = null;

  public function __construct(
    string $filePath,
    MetadataResolver $metadataResolver,
    ValueFormatter $valueFormatter,
    MappingProvider $mapping,
    ?SheetConfig $config = null
  )
  {
    try {
      $document = IOFactory::load($filePath);
      $this->metadataResolver = $metadataResolver;
      $this->valueFormatter = $valueFormatter;
      $this->mapping = $mapping;
      $sheetConfig = $config ?? $metadataResolver->getSheetConfig();

      if (isset($sheetConfig->index)) {
        $document->setActiveSheetIndex($sheetConfig->index);
      } else if (isset($sheetConfig->name)) {
        $document->setActiveSheetIndexByName($sheetConfig->name);
      }

      $this->sheet = $document->getActiveSheet();
      $this->startRow = $sheetConfig->startRow;
      $this->endRow = $sheetConfig->endRow;
      $this->initSheetHeader();
    } catch (\Exception $e) {
      throw $e;
    }
  }

  public function getErrors(): ?array
  {
    return $this->errors;
  }

  public function isValidSheet(): bool
  {
    if (!isset($this->errors)) {
     $this->initValidation(true);
    } 

    return empty($this->errors);
  }

  private function initValidation($silent = false) {
    $validationContext = new SheetValidationContext(
      $this->metadataResolver->getEntityName(),
      $this->sheetHeader,
      $this->sheet
    );
    $validationPipeline = SheetValidationPipeline::fromMetadata($this->metadataResolver);
    $this->errors = $validationPipeline->validateAll($validationContext, $silent);
  }

  public function getSheetHeader()
  {
    return $this->sheetHeader;
  }

  private function initSheetHeader()
  {
    $highestColumn = $this->sheet->getHighestColumn();
    $highestColumnIndex = Coordinate::columnIndexFromString($highestColumn);
    $currentColumn = 'A';
    $titleToLetterMapping = [];
    for ($col = 0; $col < $highestColumnIndex; $col++) {
      $cell = $this->sheet->getCell("{$currentColumn}1");
      $value = $cell->getCalculatedValue();
      if ($value !== null && $value !== '') {
        $value = strval($value);
        $titleToLetterMapping[$value] = $currentColumn;
      }
      $currentColumn++;
    }

    $this->sheetHeader = $titleToLetterMapping;
  }

  public function getColumnByTitle(string $title)
  {
    return $this->sheetHeader[$title] ?? null;
  }

  public function getData(): array
  {
    if (!isset($this->errors)) {
      $this->initValidation();
    }

    $groupedColumns = $this->mapping
      ->assembleFieldMappings($this->sheetHeader)
      ->getGroupedFields();
    $res = print_r($groupedColumns, true);
    file_put_contents(__DIR__ . '/../debug_' . date('U') . '.log', $res);
    $rowHydrator = new RowHydrator($this->metadataResolver->getEntityName(), $this->valueFormatter, $groupedColumns);
    $result = [];
    foreach ($this->sheet->getRowIterator($this->startRow, $this->endRow) as $row) {
      $result[] = ($this->mapping instanceof ModelMapping)
        ? $rowHydrator->rowToObject($row)
        : $rowHydrator->rowToArray($row);
    }
    return $result;
  }
}
