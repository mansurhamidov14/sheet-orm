<?php

namespace Twelver313\SheetORM\Mapping;

use ReflectionProperty;
use Twelver313\SheetORM\Field\FieldMapping;
use Twelver313\SheetORM\SheetHeader;

class ModelMapping extends MappingProvider
{
  private function resolve(string $field): ?FieldMapping
  {
    return $this->mappings[$field] ?? null;
  }

  /**
   * This method fills missing data from dynamic mapping 
   * with default column attributes for all properties
   */
  public function assembleFieldMappings(SheetHeader $header): MappingProvider
  {
    foreach ($this->metadataResolver->getModelProperties() as $field) {
      $fieldColumnCreated = $this->createColumnFromField($field, $header);
      if ($fieldColumnCreated) continue;

      $fieldColumnGroupListCreated = $this->createColumnGroupListFromField($field, $header);
      if ($fieldColumnGroupListCreated) continue;

      $this->createColumnGroupItemFromField($field, $header);
    }

    return $this;
  }

  private function createColumnFromField(ReflectionProperty $field, SheetHeader $header): bool
  {
    $fieldName = $field->getName();
    $fieldMapping = $this->resolve($fieldName);
    $fieldColumnAttributes = $this->metadataResolver->getColumnAttributes($fieldName);
    $headerRow = $header->getTitleRow($this->metadataResolver->getEntityName(), true);

    /** If field was not decorated as column skipping column creation for the field */
    $columnWasNotSetDynamically = !isset($fieldMapping) || (!isset($fieldMapping->column) && !isset($fieldMapping->title));
    if ($columnWasNotSetDynamically && !isset($fieldColumnAttributes)) {
      return false;
    }

    /**
     * If field mapping was already registered dynamically
     * Column title is defined, but letter wasn't
     * We are assigning corresponding column letter from header by column title
     */
    if (!isset($fieldMapping->column) && isset($fieldMapping->title)) {
      $fieldMapping->column($headerRow[$fieldMapping->title] ?? null);
    }
   
    /** Setting field mapping params from default attribute or empty array if it wasn't provided dynamically */
    if (isset($fieldMapping) && !isset($fieldMapping->params)) {
      $fieldMapping->setParams(isset($fieldColumnAttributes) ? $fieldColumnAttributes->params : []);
    }

    $defaultColumnAttrsProvided = isset($fieldColumnAttributes) && (isset($fieldColumnAttributes->title) || isset($fieldColumnAttributes->letter)); 
    /**
     * If we didn't create field mapping dynamically
     * We create it from column annotator attributes if they are provided
     */
    if (!isset($fieldMapping) && $defaultColumnAttrsProvided) {
      $this
        ->field($fieldName)
        ->column($fieldColumnAttributes->letter ?? $headerRow[$fieldColumnAttributes->title] ?? null)
        ->type($fieldColumnAttributes->type)
        ->setParams($fieldColumnAttributes->params);
      return true;
    }

    /**
     * If we are missing column from dynamic creation
     * We are assigning it from column annotator
     */
    if (!isset($fieldMapping->column) && $defaultColumnAttrsProvided) {
      $fieldMapping->column($fieldColumnAttributes->letter ?? $headerRow[$fieldColumnAttributes->title] ?? null);
    }

    /**
     * If we are missing type from dynamic creation
     * We are assigning it from column annotator
     * Unless we are missing column
     */
    if (isset($fieldMapping->column) && !isset($fieldMapping->type)) {
      $fieldMapping->type($fieldColumnAttributes->type);
      return true;
    }

    return false;
  }

  public function createColumnGroupItemFromField(ReflectionProperty $field, SheetHeader $header): bool
  {
    $fieldName = $field->getName();
    $fieldMapping = $this->resolve($fieldName);
    $groupAttributes = $this->metadataResolver->getColumnGroupItemAttributes($fieldName);
    $wasSetDynamically = $this->wasGroupSetDynamically($fieldMapping);
    /** If field was not decorated as column group item skipping column creation for the field */
    if (!$wasSetDynamically && !isset($groupAttributes)) {
      return false;
    }

    if ($wasSetDynamically) {
      $fieldMapping->columnGroup->getMappingProvider()->assembleFieldMappings($header);

      return true;
    }

    $this->field($fieldName)
      ->group($groupAttributes->target)
      ->assembleFieldMappings($header);
    return true;
  }

  public function createColumnGroupListFromField(ReflectionProperty $field, SheetHeader $header): bool
  {
    $fieldName = $field->getName();
    $fieldMapping = $this->resolve($fieldName);
    $groupAttributes = $this->metadataResolver->getColumnGroupListAttributes($fieldName);
    $wasSetDynamically = $this->wasGroupSetDynamically($fieldMapping, true);
    /** If field was not decorated as column group list skipping group list creation for the field */
    if (!$wasSetDynamically && !isset($groupAttributes)) {
      return false;
    }

    if ($wasSetDynamically) {
      $fieldMapping->columnGroup->getMappingProvider()->assembleFieldMappings($header);
      return true;
    }

    $this->field($fieldName)
      ->groupList(
        $groupAttributes->target,
        ['size' => $groupAttributes->size, 'step' => $groupAttributes->step]
      )
      ->assembleFieldMappings($header);
    return true;
  }

  private function wasGroupSetDynamically(?FieldMapping $fieldMapping, bool $isList = false): bool
  {
    return isset($fieldMapping->columnGroup)
      && $fieldMapping->columnGroup->isList() === $isList;
  }
}
