<?php

namespace Twelver313\SheetORM;

class SheetConfig
{
  public $name;
  public $index;
  public $startRow;
  public $endRow;

  public function __construct($options)
  {
    $this->index = $options['index'] ?? null;
    $this->name = $options['name'] ?? null;
    $this->startRow = $options['startRow'] ?? null;
    $this->endRow = $options['endRow'] ?? null;
  }
}
