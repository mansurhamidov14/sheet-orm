<?php

namespace Twelver313\SheetORM;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Twelver313\SheetORM\Mapping\MappingProvider;
use Twelver313\SheetORM\Mapping\ModelMapping;
use Twelver313\SheetORM\Validation\SheetValidationContext;
use Twelver313\SheetORM\Validation\SheetValidationPipeline;

class SheetHandler
{
  /** @var MetadataResolver */
  private $metadataResolver;
  /** @var Worksheet */
  private $sheet;
  /** @var Formatter */
  private $formatter;
  /** @var MappingProvider */
  private $mapping;
  private $startRow = 2;
  private $endRow = null;
  /** @var SheetHeader */
  private $sheetHeader = [];
  private $errors = null;

  public function __construct(
    string $filePath,
    MetadataResolver $metadataResolver,
    Formatter $formatter,
    MappingProvider $mapping,
    ?SheetConfig $config = null
  )
  {
    try {
      $document = IOFactory::load($filePath);
      $this->metadataResolver = $metadataResolver;
      $this->formatter = $formatter;
      $this->mapping = $mapping;
      $sheetConfig = $config ?? $metadataResolver->getSheetConfig();

      if (isset($sheetConfig->index)) {
        $document->setActiveSheetIndex($sheetConfig->index);
      } else if (isset($sheetConfig->name)) {
        $document->setActiveSheetIndexByName($sheetConfig->name);
      }
      
      $this->sheet = $document->getActiveSheet();
      $maxHeaderRow = $this->initSheetHeader();
      $this->startRow = $sheetConfig->startRow ?? $maxHeaderRow + 1;
      $this->endRow = $sheetConfig->endRow;
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

  private function initSheetHeader(): int
  {
    $this->sheetHeader = new SheetHeader();
    $headerRowToScope = $this->mapHeaderRowsToScopes();

    if (empty($headerRowToScope)) {
      return 0;
    }

    $headerRows = array_keys($headerRowToScope);
    $minHeaderRow = min($headerRows);
    $maxHeaderRow = max($headerRows);

    foreach ($this->sheet->getRowIterator($minHeaderRow, $maxHeaderRow) as $row) {
      $rowIndex = $row->getRowIndex();
      $scope = $headerRowToScope[$rowIndex] ?? null;

      if (!isset($scope)) {
        continue;
      }
      $header = [];
      foreach ($row->getCellIterator() as $cell) {
        $column = $cell->getColumn();
        $value = $cell->getCalculatedValue();
        
        if (!empty($value) && empty($header[$value])) {
          $header[$value] = $column;
        }
      }
      $this->sheetHeader->addRow($rowIndex, $scope, $header, $scope === $this->metadataResolver->getEntityName());
    }

    return $maxHeaderRow;
  }

  private function mapHeaderRowsToScopes(): array
  {
    $result = [];
    $headerRowAttributes = $this->metadataResolver->getHeaderRows();
    foreach ($headerRowAttributes as $headerRow) {
      $result[$headerRow->row] = $headerRow->scope ?? $this->metadataResolver->getEntityName();
    }


    return $result;
  }

  public function getData(): array
  {
    if (!isset($this->errors)) {
      $this->initValidation();
    }

    $groupedColumns = $this->mapping
      ->assembleFieldMappings($this->sheetHeader)
      ->getGroupedFields();
    $formatterContext = new FormatterContext($this->sheet, $this->metadataResolver, $this->sheetHeader);
    $this->formatter->setContext($formatterContext);

    $rowHydrator = new RowHydrator($this->formatter, $groupedColumns);
    $result = [];
    foreach ($this->sheet->getRowIterator($this->startRow, $this->endRow) as $row) {
      if ($this->metadataResolver->getSheetConfig()->includeEmptyRows || !$rowHydrator->isEmptyRow($row)) {
        $result[] = ($this->mapping instanceof ModelMapping)
          ? $rowHydrator->rowToObject($row)
          : $rowHydrator->rowToArray($row);
      }
    }
    return $result;
  }
}
