<?php

namespace Twelver313\SheetORM;

use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Worksheet\Row;
use ReflectionProperty;
use Twelver313\SheetORM\Field\FieldMetadata;

class RowHydrator
{
  /** @var MetadataResolver */
  private $metadataResolver;

  /** @var FieldMapping[] */
  private $groupedColumns;

  /** @var ValueFormatter */
  private $valueFormatter;

  public function __construct(MetadataResolver $metadataResolver, ValueFormatter $valueFormatter, array $groupedColumns)
  {
    $this->metadataResolver = $metadataResolver;
    $this->valueFormatter = $valueFormatter;
    $this->groupedColumns = $groupedColumns;
  }

  public function rowToObject(Row $row)
  {
    $class = $this->metadataResolver->getEntityName();
    $object = new $class;
    foreach ($row->getCellIterator() as $cell) {
      $column = $cell->getColumn();
      if (!isset($this->groupedColumns[$column])) {
        continue;
      }

      /** @var FieldMetadata */
      foreach ($this->groupedColumns[$column] as $fieldMetadata) {
        $this->fillObject($object, $fieldMetadata, $cell);
      }
    }

    return $object;
  }

  private function fillObject(object $rootObject, FieldMetadata $fieldMetadata, Cell $cell): void
  {
    $current = $rootObject;
    if (!$fieldMetadata->isRootField()) {
      foreach ($fieldMetadata->address as $step) {
        $refProperty = new ReflectionProperty($current, $step->fieldName);
        $refProperty->setAccessible(true);
        $value = $refProperty->getValue($current);
  
        if ($step->isArrayItem()) {
          $value = $value ?? [];
          // Ensure array element exists
          if (!isset($value[$step->index])) {
            $value[$step->index] = new $step->target();
          }
          $refProperty->setValue($current, $value);
          $current = $value[$step->index];
        } else {
          if (!isset($value)) {
            $value = new $step->target();
            $refProperty->setValue($current, $value);
          }
          $current = $value;
        }
      }
    }
    $refProperty = new ReflectionProperty($current, $fieldMetadata->mapping->field);
    $refProperty->setAccessible(true);
    $refProperty->setValue($current, $this->valueFormatter->format($cell, $fieldMetadata->mapping));
  }

  public function rowToArray(Row $row) {
    $result = [];
    foreach ($row->getCellIterator() as $cell) {
      $column = $cell->getColumn();
      if (!isset($this->groupedColumns[$column])) {
        continue;
      }

      foreach ($this->groupedColumns[$column] as $mapping) {
        $this->fillArray($result, $mapping, $cell);
      }
    }

    return $result;
  }

  private function fillArray(&$rootArray, FieldMetadata $fieldMetadata, Cell $cell): void
  {
    if (!$fieldMetadata->isRootField()) {
      $current = &$rootArray;
      foreach ($fieldMetadata->address as $step) {
        if ($step->isArrayItem()) {
          if (!isset($current[$step->fieldName])) {
            $current[$step->fieldName] = [];
          }
          if (!isset($current[$step->fieldName][$step->index])) {
            $current[$step->fieldName][$step->index] = [];
          }
          $current = &$current[$step->fieldName][$step->index];
        } else {
          if (!isset($current[$step->fieldName])) {
            $current[$step->fieldName] = [];
          }
          $current = &$current[$step->fieldName];
        }
      }
      $current[$fieldMetadata->mapping->field] = $this->valueFormatter->format($cell, $fieldMetadata->mapping);
    } else {
      $rootArray[$fieldMetadata->mapping->field] = $this->valueFormatter->format($cell, $fieldMetadata->mapping);
    }
  }

  public function isEmptyRow(Row $row, $startColumn = 'A', $endColumn = null): bool
  {
    foreach ($row->getCellIterator($startColumn, $endColumn) as $cell) {
      $value = $cell->getCalculatedValue();
      $value = strval($value);
      if (trim($value) !== '') {
        return false;
      }
    }

    return true;
  }
}
