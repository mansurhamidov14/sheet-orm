<?php

namespace Twelver313\SheetORM;

use PhpOffice\PhpSpreadsheet\Worksheet\Row;
use ReflectionProperty;
use Twelver313\SheetORM\Exceptions\MissingValueFormatterException;
use Twelver313\SheetORM\Field\FieldMetadata;

class RowHydrator
{
  /** @var Field\FieldMapping[] */
  private $groupedColumns;

  /** @var Formatter */
  private $formatter;

  public function __construct(Formatter $formatter, array $groupedColumns)
  {
    $this->formatter = $formatter;
    $this->groupedColumns = $groupedColumns;
  }

  public function rowToObject(Row $row)
  {
    $class = $this->formatter->context->metadata->getEntityName();
    $this->formatter->context->row = $row;
    $object = new $class();
    foreach ($row->getCellIterator() as $cell) {
      $this->formatter->context->cell = $cell;
      $column = $cell->getColumn();
      if (!isset($this->groupedColumns[$column])) {
        continue;
      }

      /** @var FieldMetadata */
      foreach ($this->groupedColumns[$column] as $fieldMetadata) {
        $this->formatter->context->fieldMapping =
          $fieldMetadata->mapping;
        $this->fillObject($object, $fieldMetadata);
      }
    }

    return $object;
  }

  /**
   * @throws \ReflectionException
   * @throws MissingValueFormatterException
   */
  private function fillObject(object $rootObject, FieldMetadata $fieldMetadata): void
  {
    $current = $rootObject;
    $finalValue = $this->formatter->format(
      $fieldMetadata->mapping->type,
      $fieldMetadata->mapping->params
    );
    if (!isset($finalValue)) {
      return;
    }
    if (!$fieldMetadata->isRootField()) {
      foreach ($fieldMetadata->address as $step) {
        $refProperty = new ReflectionProperty(
          $current,
          $step->fieldName
        );
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
    $refProperty->setValue($current, $this->formatter->format(
      $fieldMetadata->mapping->type,
      $fieldMetadata->mapping->params
    ));
  }

  public function rowToArray(Row $row): array
  {
    $this->formatter->context->row = $row;
    $result = [];
    foreach ($row->getCellIterator() as $cell) {
      $this->formatter->context->cell = $cell;
      $column = $cell->getColumn();
      if (!isset($this->groupedColumns[$column])) {
        continue;
      }

      foreach ($this->groupedColumns[$column] as $fieldMetadata) {
        $this->formatter->context->fieldMapping = $fieldMetadata->mapping;
        $this->fillArray($result, $fieldMetadata);
      }
    }

    return $result;
  }

  private function fillArray(&$rootArray, FieldMetadata $fieldMetadata): void
  {
    $current = &$rootArray;
    $finalValue = $this->formatter->format(
      $fieldMetadata->mapping->type,
      $fieldMetadata->mapping->params
    );
    if (!isset($finalValue)) {
      return;
    }
    if (!$fieldMetadata->isRootField()) {
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
    }
    $current[$fieldMetadata->mapping->field] = $this->formatter->format(
      $fieldMetadata->mapping->type,
      $fieldMetadata->mapping->params
    );
  }

  public function isEmptyRow(Row $row, $startColumn = "A", $endColumn = null): bool
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
