<?php

namespace Twelver313\Sheetmap;

use ReflectionClass;
use Twelver313\Sheetmap\Column;

class SpreadsheetMapper
{
  public function init($className)
  {
    $this->mapRowToObject($className);
  }
  
  private function mapRowToObject(string $className)
  {
    $object = new $className();
    $refClass = new ReflectionClass($className);

    foreach ($refClass->getProperties() as $property) {
      foreach ($property->getAttributes(Column::class) as $attr) {
        $column = $attr->newInstance();
        var_dump($column);
      }
    }
  }
}