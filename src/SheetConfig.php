<?php

namespace Twelver313\Sheetmap;

class SheetConfig
{
  public $headerRow;
  public $name;
  public $index;
  public $startRow;
  public $endRow;

  public function __construct($options)
  {
    $this->headerRow = $options['headerRow'] ?? 1;
    $this->index = $options['index'] ?? null;
    $this->name = $options['name'] ?? null;
    $this->startRow = $options['startRow'] ?? 2;
    $this->endRow = $options['endRow'] ?? null;
  }
}
