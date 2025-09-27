<?php

namespace Twelver313\Sheetmap;

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\RowIterator;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Twelver313\Sheetmap\Exceptions\SpreadsheetReaderException;
use Twelver313\Sheetmap\MetadataResolver;
use Twelver313\Sheetmap\Validation\ValidationContext;
use Twelver313\Sheetmap\Validation\ValidationPipeline;

class SpreadsheetEngine
{
  /** @var Worksheet */
  private $sheet;
  private $startRow = 2;
  private $endRow = null;
  private $sheetHeader = [];

  public function loadFile(string $filePath, MetadataResolver $metadataResolver, SheetConfigInterface|null $config = null): self
  {
    try {
      $document = IOFactory::load($filePath);
      $sheetConfig = $sheetConfig ?? $metadataResolver->getSheetConfig();

      if (isset($sheetConfig->index)) {
        $document->setActiveSheetIndex($sheetConfig->index);
      } else if (isset($sheetConfig->name)) {
        $document->setActiveSheetIndexByName($sheetConfig->name);
      }

      $this->sheet = $document->getActiveSheet();
      $this->startRow = $sheetConfig->startRow;
      $this->endRow = $sheetConfig->endRow;
      $this->retrieveSheetHeader();
      $validationContext = new ValidationContext($metadataResolver->getClass(), $this->sheetHeader, $this->sheet);
      $validationPipeline = ValidationPipeline::fromMetadata($metadataResolver);
      $validationPipeline->validateAll($validationContext);

      return $this;
    } catch (\PhpOffice\PhpSpreadsheet\Reader\Exception $e) {
      throw new SpreadsheetReaderException($e->getMessage(), $e->getCode(), $e->getPrevious());
    }
  }

  public function getSheetHeader()
  {
    return $this->sheetHeader;
  }

  private function retrieveSheetHeader()
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

  public function fetchRows(): RowIterator
  {
    return $this->sheet->getRowIterator($this->startRow, $this->endRow);
  }
}
