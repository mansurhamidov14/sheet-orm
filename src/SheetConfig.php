<?php

namespace Twelver313\Sheetmap;

use Twelver313\Sheetmap\SheetConfigInterface;

class SheetConfig implements SheetConfigInterface
{
  public $name;
  public $index;
  public $startRow;
  public $endRow;

  public function __construct($options)
  {
    $this->index = $options['index'] ?? null;
    $this->name = $options['name'] ?? null;
    $this->startRow = $options['startRow'] ?? 2;
    $this->endRow = $options['endRow'];
  }
}
