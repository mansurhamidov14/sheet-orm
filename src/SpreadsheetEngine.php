<?php

namespace Twelver313\Sheetmap;

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\RowIterator;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Twelver313\Sheetmap\MetadataResolver;

class SpreadsheetEngine
{
  /**
   * @var Worksheet
   */
  private $sheet;
  private $startRow = 2;
  private $endRow = null;
  private $sheetHeader = [];

  public function loadFile(string $filePath, MetadataResolver $metadataResolver): self
  {
    try {
      $document = IOFactory::load($filePath);
      $sheetConfig = $metadataResolver->getSheetConfig();

      if (isset($sheetConfig->index)) {
        $document->setActiveSheetIndex($sheetConfig->index);
      } else if (isset($sheetConfig->name)) {
        $document->setActiveSheetIndexByName($sheetConfig->name);
      }

      $this->setSheet($document->getActiveSheet());
      $this->setStartRow($sheetConfig->startRow);
      $this->setEndRow($sheetConfig->endRow);
      $this->retrieveSheetHeader();
      return $this;
    } catch (\PhpOffice\PhpSpreadsheet\Reader\Exception $e) {
      throw $e;
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

  public function setSheet(Worksheet $sheet)
  {
    $this->sheet = $sheet;
  }

  public function setStartRow(int $row)
  {
    $this->startRow = $row;
  }

  public function setEndRow(int|null $row)
  {
    $this->endRow = $row;
  }

  public function fetchRows(): RowIterator
  {
    return $this->sheet->getRowIterator($this->startRow, $this->endRow);
  }
}