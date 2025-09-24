<?php

namespace Twelver313\Sheetmap;

use PhpOffice\PhpSpreadsheet\Worksheet\Row;
use Twelver313\Sheetmap\ValueFormatter;
use ReflectionProperty;

class RowHydrator
{
  private $className;

  /** @var PropertyMapping[] */
  private $groupedColumns;

  /** @var ValueFormatter */
  private $valueFormatter;

  public function __construct(string $className, ValueFormatter $valueFormatter, array $groupedColumns)
  {
    $this->className = $className;
    $this->valueFormatter = $valueFormatter;
    $this->groupedColumns = $groupedColumns;
  }

  public function hydrate(Row $row)
  {
    $object = new ($this->className)();
    foreach ($row->getCellIterator() as $cell) {
      $column = $cell->getColumn();
      if (!isset($this->groupedColumns[$column])) {
        continue;
      }

      foreach ($this->groupedColumns[$column] as $mapping) {
        $refProperty = new ReflectionProperty($object, $mapping->property);
        $refProperty->setAccessible(true);
        $refProperty->setValue($object, $this->valueFormatter->format($cell, $mapping->type));
      }
    }

    return $object;
  }
}