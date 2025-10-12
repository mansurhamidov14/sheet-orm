<?php

namespace Twelver313\SheetORM;

use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class FormatterContext
{
  /** @var \PhpOffice\PhpSpreadsheet\Cell\Cell */
  public $cell;

  /** @var \PhpOffice\PhpSpreadsheet\Worksheet\Row */
  public $row;

  /** @var Worksheet */
  public $sheet;

  /** @var SheetHeader */
  public $sheetHeader;

  /** @var MetadataResolver */
  public $metadata;

  /** @var Field\FieldMapping */
  public $fieldMapping;

  /** @var Formatter */
  public $formatter;

  public function __construct(
    Worksheet $sheet,
    MetadataResolver $metadata,
    SheetHeader $sheetHeader
  )
  {
    $this->metadata = $metadata;
    $this->sheet = $sheet;
    $this->sheetHeader = $sheetHeader;
  }

  public function getHeaderTitle(?string $scope = null)
  {
    return $this->sheetHeader->getTitle($this->cell->getColumn(), $scope);
  }
}
