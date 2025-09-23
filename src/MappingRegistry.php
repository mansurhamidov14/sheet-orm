<?php

namespace Twelver313\Sheetmap;

use ReflectionClass;

class MappingRegistry
{
  /** @var */
  private $mappings;

  public function __construct()
  {
    $this->mappings = [];
  }

  public function registerIfNotExist(string $class): ClassMapping
  {
    if ($this->mappingExists($class)) {
      return $this->mappings[$class];
    }

    return $this->registerNew($class);
  }

  public function registerNew(string $class): ClassMapping
  {
    $this->mappings[$class] = new ClassMapping();
    return $this->mappings[$class];
  }

  public function mappingExists(string $class)
  {
    return isset($this->mappings[$class]);
  }

  public function resolve(string $class, string $property): PropertyMapping|null
  {
    if (!$this->mappingExists($class)) {
      return null;
    }

    return $this->mappings[$class]->getMappings()[$property] ?? null;
  }

  public function fulfillMissingProperties(ReflectionClass $refClass, array $header): self
  {
    $mainClass = $refClass->getName();
    $mappingClass = $this->registerIfNotExist($mainClass);

    foreach ($refClass->getProperties() as $property) {
      $propertyMapping = $this->resolve($mainClass, $property->getName());
      $propertyAttributes = ReflectionUtils::findAttributeInstanceOnProperty($refClass, $property->getName(), Column::class);

      if (
        isset($propertyMapping) &&
        !isset($propertyMapping->column) &&
        isset($propertyMapping->title)
      ) {
        $propertyMapping->column($header[$propertyMapping->title] ?? null);
      }

      if (!isset($propertyAttributes)) {
        continue;
      }

      if (!isset($propertyMapping) && (isset($propertyAttributes->title) || isset($propertyAttributes->letter))) {
        $propertyMapping = $mappingClass
          ->property($property->name)
          ->column($propertyAttributes->letter ?? $header[$propertyAttributes->title] ?? null)
          ->type($propertyAttributes->type);
        continue;
      }

      if (isset($propertyMapping->column) && !isset($propertyMapping->type)) {
        $propertyMapping->type($propertyAttributes->type);
      }
    }

    return $this;
  }

  public function getGroupedProperties($class)
  {
    if (!$this->mappingExists($class)) {
      return [];
    }

    $result = [];
    foreach ($this->mappings[$class]->getMappings() as $propertyMapping) {
      if (!isset($propertyMapping->column)) {
        continue;
      }

      $result[$propertyMapping->column][] = $propertyMapping;
    }

    return $result;
  }
}