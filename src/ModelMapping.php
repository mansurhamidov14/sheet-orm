<?php

namespace Twelver313\Sheetmap;

use Twelver313\Sheetmap\MetadataResolver;
use Twelver313\Sheetmap\PropertyMapping;

class ModelMapping
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
    return $this->mappings[$property] = new PropertyMapping(
      $property,
      $this->metadataResolver->getModel()
    );
  }

  public function getMappings(): array {
    return $this->mappings;
  }

  public function setMappings($property, $mapping) {
    $this->mappings[$property] = $mapping;
  }

  private function resolve(string $property): ?PropertyMapping
  {
    return $this->mappings[$property] ?? null;
  }

  /**
   * This method fulfills missing data from dynamic mapping 
   * with default column attributes for all properties
   */
  public function fulfillMissingProperties(array $header): self
  {
    foreach ($this->metadataResolver->getModelProperties() as $property) {
      $propertyMapping = $this->resolve($property->getName());
      $propertyAttributes = $this->metadataResolver->getColumnAttributes($property->getName());

      /**
       * If property mapping was already registered dynamically
       * SheetColumn column title is defined, but letter wasn't
       * We are assigning corresponding column letter from header by column title
      */
      if (
        isset($propertyMapping) &&
        !isset($propertyMapping->column) &&
        isset($propertyMapping->title)
      ) {
        $propertyMapping->column($header[$propertyMapping->title] ?? null);
      }

      /** 
       * If we didn't assign column attributes by default by using SheetColumn annotator
       * We have nothing to do and skip current property
      */
      if (!isset($propertyAttributes)) {
        continue;
      }

      $defaultColumnAttrsProvided = isset($propertyAttributes->title) || isset($propertyAttributes->letter);
      /**
       * If we didn't create property mapping dynamically
       * We create it from column annotator attributes if they are provided
       */
      if (!isset($propertyMapping) && $defaultColumnAttrsProvided) {
        $propertyMapping = $this
          ->property($property->name)
          ->column($propertyAttributes->letter ?? $header[$propertyAttributes->title] ?? null)
          ->type($propertyAttributes->type);
        continue;
      }

      /**
       * If we are missing column from dynamic creation
       * We are assigning it from column annotator
       */
      if (!isset($propertyMapping->column) && $defaultColumnAttrsProvided) {
        $propertyMapping->column($propertyAttributes->letter ?? $header[$propertyAttributes->title] ?? null);
      }

      /**
       * If we are missing type from dynamic creation
       * We are assigning it from column annotator
       * Unless we are missing column
       */
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
