<?php

namespace Twelver313\Sheetmap;

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Twelver313\Sheetmap\Exceptions\SpreadsheetReaderException;
use Twelver313\Sheetmap\MetadataResolver;
use Twelver313\Sheetmap\Validation\ValidationContext;
use Twelver313\Sheetmap\Validation\ValidationPipeline;

class SheetHandler
{
  /** @var MetadataResolver */
  private $metadataResolver;
  /** @var Worksheet */
  private $sheet;
  /** @var ValueFormatter */
  private $valueFormatter;
  /** @var ModelMapping */
  private $modelMapping;
  private $startRow = 2;
  private $endRow = null;
  private $sheetHeader = [];
  private $errors = null;

  public function __construct(
    string $filePath,
    MetadataResolver $metadataResolver,
    ValueFormatter $valueFormatter,
    ModelMapping $modelMapping,
    SheetConfigInterface|null $config = null
  )
  {
    try {
      $document = IOFactory::load($filePath);
      $this->metadataResolver = $metadataResolver;
      $this->valueFormatter = $valueFormatter;
      $this->modelMapping = $modelMapping;
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
      throw new SpreadsheetReaderException($e->getMessage(), $e->getCode(), $e->getPrevious());
    }
  }

  public function getErrors(): array|null
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
    $validationContext = new ValidationContext(
      $this->metadataResolver->getModel(),
      $this->sheetHeader,
      $this->sheet
    );
    $validationPipeline = ValidationPipeline::fromMetadata($this->metadataResolver);
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
      $value = $cell->getValue();
      if ($value !== null && $value !== '') {
        $value = strval($value);
        $titleToLetterMapping[$value] = $currentColumn;
        $currentColumn++;
      }
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

    $groupedColumns = $this->modelMapping->fulfillMissingProperties($this->sheetHeader)->getGroupedProperties();
    $rowHydrator = new RowHydrator($this->metadataResolver->getModel(), $this->valueFormatter, $groupedColumns);
    $result = [];
    foreach ($this->sheet->getRowIterator($this->startRow, $this->endRow) as $row) {
      $result[] = $rowHydrator->rowToObject($row);
    }
    return $result;
  }
}
