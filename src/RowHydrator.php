<?php

namespace Twelver313\Sheetmap;

use PhpOffice\PhpSpreadsheet\Worksheet\Row;
use Twelver313\Sheetmap\ValueFormatter;
use ReflectionProperty;

class RowHydrator
{
  private $modelName;

  /** @var PropertyMapping[] */
  private $groupedColumns;

  /** @var ValueFormatter */
  private $valueFormatter;

  public function __construct(string $modelName, ValueFormatter $valueFormatter, array $groupedColumns)
  {
    $this->modelName = $modelName;
    $this->valueFormatter = $valueFormatter;
    $this->groupedColumns = $groupedColumns;
  }

  public function rowToObject(Row $row)
  {
    $object = new ($this->modelName)();
    foreach ($row->getCellIterator() as $cell) {
      $column = $cell->getColumn();
      if (!isset($this->groupedColumns[$column])) {
        continue;
      }

      foreach ($this->groupedColumns[$column] as $mapping) {
        $refProperty = new ReflectionProperty($object, $mapping->property);
        $refProperty->setAccessible(true);
        $refProperty->setValue($object, $this->valueFormatter->format($cell, $mapping));
      }
    }

    return $object;
  }
}
