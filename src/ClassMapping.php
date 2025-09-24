<?php

namespace Twelver313\Sheetmap;

use Twelver313\Sheetmap\PropertyMapping;

class ClassMapping
{
  /** @var PropertyMapping[] */
  private $mappings = [];

  /** @var MetadataResolver */
  private $metadataResolver;

  public function __construct(MetadataResolver $metadataResolver)
  {
    $this->metadataResolver = $metadataResolver;
  }

  public function property(string $property): PropertyMapping {
    return $this->mappings[$property] = new PropertyMapping($property);
  }

  public function getMappings(): array {
    return $this->mappings;
  }

  public function setMappings($property, $mapping) {
    $this->mappings[$property] = $mapping;
  }

  private function resolve(string $property): PropertyMapping|null
  {
    return $this->mappings[$property] ?? null;
  }

  public function fulfillMissingProperties(array $header): self
  {
    foreach ($this->metadataResolver->getClassProperties() as $property) {
      $propertyMapping = $this->resolve($property->getName());
      $propertyAttributes = $this->metadataResolver->getColumnAttributes($property->getName());

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
        $propertyMapping = $this
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

  public function getGroupedProperties()
  {
    $result = [];
    foreach ($this->mappings as $propertyMapping) {
      if (!isset($propertyMapping->column)) {
        continue;
      }

      $result[$propertyMapping->column][] = $propertyMapping;
    }

    return $result;
  }
}
