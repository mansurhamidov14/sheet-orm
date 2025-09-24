<?php

namespace Twelver313\Sheetmap;

use \ReflectionClass;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use ReflectionProperty;
use Twelver313\Sheetmap\Sheet;
use Twelver313\Sheetmap\ValueFormatter;

class SpreadsheetMapper
{
  /**
   * @var MappingRegistry
   */
  private $mappingRegistry;
  /**
   * @var string
   */
  private $loadedClass;
  /**
   * @var ReflectionClass
   */
  private $refClass;
  /**
   * @var Worksheet
   */
  private $sheet;

  /**
   * @var ValueFormatter
   */
  public $valueFormatter;

  /**
   * @var SpreadsheetEngine
   */
  private $spreadsheetEngine;

  public function __construct()
  {
    $this->mappingRegistry = new MappingRegistry();
    $this->valueFormatter = new ValueFormatter();
    $this->spreadsheetEngine = new SpreadsheetEngine();
  }

  public function load($className): self
  {
    $this->loadedClass = $className;
    $this->refClass = new ReflectionClass($className);
    return $this;
  }

  public function fromFile(string $filePath): array
  {
    $this->spreadsheetEngine = new SpreadsheetEngine();
    $this->spreadsheetEngine->loadFile($filePath, $this->getLoadOptions());
    $groupedColumns = $this->mappingRegistry
      ->fulfillMissingProperties($this->refClass, $this->spreadsheetEngine->getSheetHeader())
      ->getGroupedProperties($this->refClass->getName());

    $result = [];
    foreach ($this->spreadsheetEngine->fetchRows() as $row)
    {
      $object = new $this->loadedClass();
      foreach ($row->getCellIterator() as $cell) {
        $column = $cell->getColumn();
        if (!isset($groupedColumns[$column])) {
          continue;
        }

        foreach ($groupedColumns[$column] as $mapping) {
          $refProperty = new ReflectionProperty($object, $mapping->property);
          $refProperty->setAccessible(true);
          $refProperty->setValue($object, $this->valueFormatter->format($cell, $mapping->type));
        }
      }

      $result[] = $object;
    }

    return $result;
  }

  public function map(string $className, callable $callback)
  {
    $mapping = $this->mappingRegistry->registerNew($className);
    $callback($mapping);
  }

  public function getSheet(): Worksheet
  {
    if (!isset($this->sheet)) {
      throw new \Exception('Document file was not selected');
    }

    return $this->sheet;
  }

  private function getLoadOptions()
  {
    $sheetData = $this->refClass->getAttributes(Sheet::class);
    if (count($sheetData)) {
      $sheetData = $sheetData[0]->newInstance();
      return [
        'name' => $sheetData->name,
        'index' => $sheetData->index,
        'startRow' => $sheetData->startRow,
        'endRow' => $sheetData->endRow
      ];
    }

    return [];
  }
}