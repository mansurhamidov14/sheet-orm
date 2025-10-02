<?php

namespace Twelver313\Sheetmap;

use Twelver313\Sheetmap\ModelMetadata;
use Twelver313\Sheetmap\FieldMapping;

class ModelMapping implements MappingProvider
{
  /** @var FieldMapping[] */
  private $mappings = [];

  /** @var ModelMetadata */
  private $metadataResolver;

  public function __construct(ModelMetadata $metadataResolver)
  {
    $this->metadataResolver = $metadataResolver;
  }

  public function field(string $field): FieldMapping {
    return $this->mappings[$field] = new FieldMapping(
      $field,
      $this->metadataResolver->getEntityName()
    );
  }

  private function resolve(string $field): ?FieldMapping
  {
    return $this->mappings[$field] ?? null;
  }

  /**
   * This method fulfills missing data from dynamic mapping 
   * with default column attributes for all properties
   */
  public function assembleFieldMappings(array $header): self
  {
    foreach ($this->metadataResolver->getModelProperties() as $field) {
      $fieldMapping = $this->resolve($field->getName());
      $fieldAttributes = $this->metadataResolver->getColumnAttributes($field->getName());

      /**
       * If field mapping was already registered dynamically
       * SheetColumn column title is defined, but letter wasn't
       * We are assigning corresponding column letter from header by column title
      */
      if (
        isset($fieldMapping) &&
        !isset($fieldMapping->column) &&
        isset($fieldMapping->title)
      ) {
        $fieldMapping->column($header[$fieldMapping->title] ?? null);
      }

      /** 
       * If we didn't assign column attributes by default by using SheetColumn annotator
       * We have nothing to do and skip current field
      */
      if (!isset($fieldAttributes)) {
        continue;
      }

      $defaultColumnAttrsProvided = isset($fieldAttributes->title) || isset($fieldAttributes->letter);
      /**
       * If we didn't create field mapping dynamically
       * We create it from column annotator attributes if they are provided
       */
      if (!isset($fieldMapping) && $defaultColumnAttrsProvided) {
        $fieldMapping = $this
          ->field($field->name)
          ->column($fieldAttributes->letter ?? $header[$fieldAttributes->title] ?? null)
          ->type($fieldAttributes->type);
        continue;
      }

      /**
       * If we are missing column from dynamic creation
       * We are assigning it from column annotator
       */
      if (!isset($fieldMapping->column) && $defaultColumnAttrsProvided) {
        $fieldMapping->column($fieldAttributes->letter ?? $header[$fieldAttributes->title] ?? null);
      }

      /**
       * If we are missing type from dynamic creation
       * We are assigning it from column annotator
       * Unless we are missing column
       */
      if (isset($fieldMapping->column) && !isset($fieldMapping->type)) {
        $fieldMapping->type($fieldAttributes->type);
      }
    }

    return $this;
  }

  public function getGroupedFields(): array
  {
    $result = [];
    foreach ($this->mappings as $fieldMapping) {
      if (!isset($fieldMapping->column)) {
        continue;
      }

      $result[$fieldMapping->column][] = $fieldMapping;
    }

    return $result;
  }
}
