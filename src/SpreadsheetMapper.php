<?php

namespace Twelver313\Sheetmap;

use \ReflectionClass;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Twelver313\Sheetmap\ClassMapping;
use Twelver313\Sheetmap\Column;
use Twelver313\Sheetmap\Sheet;

class SpreadsheetMapper
{
  /**
   * @var array
   */
  private $classMappings;
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

  public function __construct()
  {
    $this->classMappings = [];
  }

  public function load($className): self
  {
    $this->loadedClass = $className;
    $this->refClass = new ReflectionClass($className);
    return $this;
  }

  public function fromFile($filePath): array
  {
    $document = IOFactory::load($filePath);
    $sheetData = $this->refClass->getAttributes(Sheet::class);
    if (count($sheetData)) {
      $sheetData = $sheetData[0]->newInstance();
      $sheetName = $sheetData->sheet;
      $sheetName
        ? $document->setActiveSheetIndexByName($sheetName)
        : $document->setActiveSheetIndex($sheetData->sheetIndex);
    }

    $this->sheet = $document->getActiveSheet();
    $propertiesMap = $this->mapAllProperties();

    $result = [];
    foreach ($this->sheet->getRowIterator(2) as $row)
    {
      $object = new $this->loadedClass();
      foreach ($row->getCellIterator() as $cell) {
        $column = $cell->getColumn();
        if (isset($propertiesMap[$column])) {
          $object->{$propertiesMap[$column]->property} = ValueFormatter::formatValue($cell, $propertiesMap[$column]->type);
        }
      }

      $result[] = $object;
    }

    return $result;
  }

  public function map(string $className, callable $callback)
  {
    $mapping = new ClassMapping();
    $callback($mapping);
    $this->classMappings[$className] = $mapping;
  }

  public function getSheet(): Worksheet
  {
    if (!isset($this->sheet)) {
      throw new \Exception('Document file was not selected');
    }

    return $this->sheet;
  }

  private function getSheetHeader()
  {
    $highestColumn = $this->getSheet()->getHighestColumn();
    $highestColumnIndex = Coordinate::columnIndexFromString($highestColumn);
    $currentColumn = 'A';
    $titleToColumnMapping = [];
    $lastFilledColumn = $currentColumn;
    for ($col = 0; $col < $highestColumnIndex; $col++) {
      $cell = $this->sheet->getCell("{$currentColumn}1");
      $value = $cell->getValue();
      if ($value !== null && $value !== '') {
        $value = strval($value);
        $titleToColumnMapping[$value] = $currentColumn;
        $lastFilledColumn = $currentColumn;
        $currentColumn++;
      }
    }

    return [
      'highestColumn' => $lastFilledColumn,
      'columns' => $titleToColumnMapping
    ];
  }

  private function mapAllProperties() {
    $headerColumns = $this->getSheetHeader();
    $result = [];
    foreach ($this->refClass->getProperties() as $property) {
      $propertyMapping = $this->resolvePropertyMapping($property->name, $headerColumns);
      $result[$propertyMapping->columnLetter] = $propertyMapping;
    }

    return $result;
  }

  private function resolvePropertyMapping(string $property, array $headerColumns): PropertyMapping
  {
    $classMapping = $this->classMappings[$this->loadedClass] ?? null;

    if (isset($classMapping) && isset($classMapping->getMappings()[$property])) {
      /** @var PropertyMapping $propertyMapping */
      $propertyMapping = $classMapping->getMapping()[$property];
    } else {
      $propertyMapping = new PropertyMapping($property);
      $this->classMappings[$this->loadedClass] = new ClassMapping();
      $this->classMappings[$this->loadedClass]->setMappings($property, $propertyMapping);
    }

    $defaultColumnProperties = ReflectionUtils::findAttributeInstanceOnProperty($this->refClass, $property, Column::class);

    if (!isset($defaultColumnProperties)) {
      return $propertyMapping;
    }

    if (!isset($propertyMapping->column) && isset($defaultColumnProperties->title)) {
      $propertyMapping->column($defaultColumnProperties->title);
    }

    if (!isset($propertyMapping->columnLetter) && isset($defaultColumnProperties->letter)) {
      $propertyMapping->columnLetter($defaultColumnProperties->letter);
    } elseif (!isset($propertyMapping->columnLetter)) {
      $propertyMapping->columnLetter($headerColumns['columns'][$propertyMapping->column] ?? "A");
    }

    if (!isset($propertyMapping->type) && isset($defaultColumnProperties->type)) {
      $propertyMapping->type($defaultColumnProperties->type);
    }

    return $propertyMapping;
  }
}