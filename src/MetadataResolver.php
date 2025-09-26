<?php

namespace Twelver313\Sheetmap;

use ReflectionClass;
use ReflectionProperty;
use Twelver313\Sheetmap\Sheet;
use Twelver313\Sheetmap\SheetConfigInterface;
use Twelver313\Sheetmap\Column;

class MetadataResolver
{
  /** @var ReflectionClass */
  private $refClass;

  public function __construct(string $class)
  {
    $this->refClass = new ReflectionClass($class);
  }

  public function getClass()
  {
    return $this->refClass->getName();
  }

  public function findPropertyByName(string $property): ReflectionProperty|null
  {
    return $this->refClass->hasProperty($property)
      ? $this->refClass->getProperty($property)
      : null;
  }

  public function getSheetConfig(): SheetConfigInterface
  {
    $sheetConfigAttribute = $this->refClass->getAttributes(Sheet::class)[0] ?? null;
    if (isset($sheetConfigAttribute)) {
      return $sheetConfigAttribute->newInstance();
    }

    return new Sheet();
  }

  public function getPropertyAttributes(string $property, string $attributeClass): object|null
  {
    if (!$this->refClass->hasProperty($property)) {
      return null;
    }

    $refProperty = $this->refClass->getProperty($property);
    $propertyAttributes = $refProperty->getAttributes($attributeClass);

    if (!count($propertyAttributes)) {
      return null;
    }

    return $propertyAttributes[0]->newInstance();
  }

  public function getColumnAttributes($property): Column|null
  {
    return $this->getPropertyAttributes($property, Column::class);
  }

  public function getClassProperties()
  {
    return $this->refClass->getProperties();
  }

  /** 
   * @return \ReflectionAttribute<Validation>[]
   */
  public function getValidationAttributes(): array
  {
    return $this->refClass->getAttributes(Validation::class);
  }
}
