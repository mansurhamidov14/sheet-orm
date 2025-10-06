<?php

namespace Twelver313\Sheetmap\Attributes;

class ColumnGroup
{
  public $headerRow;

  public function __construct(int $headerRow = 1)
  {
    $this->headerRow = $headerRow;
  }
}
