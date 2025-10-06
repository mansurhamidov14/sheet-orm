<?php

namespace Twelver313\Sheetmap;

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use ReflectionProperty;
use Twelver313\Sheetmap\ModelMetadata;
use Twelver313\Sheetmap\FieldMapping;

class ModelMapping implements MappingProvider
{
  /** @var FieldMapping[] */
  private $mappings = [];

  /** @var ModelMetadata */
  private $metadataResolver;

  public function __construct(MetadataResolver $metadataResolver)
  {
    $this->metadataResolver = $metadataResolver;
  }

  public function getMappings()
  {
    return $this->mappings;
  }

  public function field(string $field): FieldMapping
  {
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
   * This method fills missing data from dynamic mapping 
   * with default column attributes for all properties
   */
  public function assembleFieldMappings(array $header): MappingProvider
  {
    foreach ($this->metadataResolver->getModelProperties() as $field) {
      $fieldColumnCreated = $this->createColumnFromField($field, $header);
      if ($fieldColumnCreated) continue;


      $fieldColumnGroupItemCreated = $this->createColumnGroupItemFromField($field, $header);
      if ($fieldColumnGroupItemCreated) continue;

      $this->createColumnGroupListFromField($field, $header);
    }

    return $this;
  }

  public function createColumnFromField(ReflectionProperty $field, array $header): bool
  {
    $fieldName = $field->getName();
    $fieldMapping = $this->resolve($fieldName);
    $fieldColumnAttributes = $this->metadataResolver->getColumnAttributes($fieldName);

    /** If field was not decorated as column skipping column creation for the field */
    $columnWasNotSetDynamically = !isset($fieldMapping) || (!isset($fieldMapping->column) && !isset($fieldMapping->title));
    if ($columnWasNotSetDynamically && !isset($fieldColumnAttributes)) {
      return false;
    }

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

    $defaultColumnAttrsProvided = isset($fieldColumnAttributes) && isset($fieldColumnAttributes->title) || isset($fieldColumnAttributes->letter);
    /**
     * If we didn't create field mapping dynamically
     * We create it from column annotator attributes if they are provided
     */
    if (!isset($fieldMapping) && $defaultColumnAttrsProvided) {
      $fieldMapping = $this
        ->field($fieldName)
        ->column($fieldColumnAttributes->letter ?? $header[$fieldColumnAttributes->title] ?? null)
        ->type($fieldColumnAttributes->type);
      return true;
    }

    /**
     * If we are missing column from dynamic creation
     * We are assigning it from column annotator
     */
    if (!isset($fieldMapping->column) && $defaultColumnAttrsProvided) {
      $fieldMapping->column($fieldColumnAttributes->letter ?? $header[$fieldColumnAttributes->title] ?? null);
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

  public function createColumnGroupItemFromField(ReflectionProperty $field, array $header)
  {
    $fieldName = $field->getName();
    $fieldMapping = $this->resolve($fieldName);
    $fieldColumnGroupItemAttributes = $this->metadataResolver->getColumnGroupItemAttributes($fieldName);
    /** If field was not decorated as column group item skipping column creation for the field */
    $columnGroupItemWasNotSetDynamically = !isset($fieldMapping) || !isset($fieldMapping->groupItem);
    if ($columnGroupItemWasNotSetDynamically && !isset($fieldColumnGroupItemAttributes)) {
      return false;
    }

    if (isset($fieldMapping) && isset($fieldMapping->groupItem)) {
      $fieldMapping->groupItem->assembleFieldMappings($header);

      return true;
    }

    $this->field($fieldName)
      ->groupItem($fieldColumnGroupItemAttributes->target)
      ->assembleFieldMappings($header);
    return true;
  }

  public function createColumnGroupListFromField(ReflectionProperty $field, array $header)
  {
    $fieldName = $field->getName();
    $fieldMapping = $this->resolve($fieldName);
    $fieldColumnGroupListAttributes = $this->metadataResolver->getColumnGroupListAttributes($fieldName);

    /** If field was not decorated as column group list skipping group list creation for the field */
    $columnGroupListWasNotSetDynamically = !isset($fieldMapping) || !isset($fieldMapping->groupList);
    if ($columnGroupListWasNotSetDynamically && !isset($fieldColumnGroupListAttributes)) {
      return false;
    }

    if (isset($fieldMapping) && isset($fieldMapping->groupList)) {
      $fieldMapping->groupList['mappingProvider']->assembleFieldMappings($header);
      return true;
    }

    $this->field($fieldName)
      ->groupList(
        $fieldColumnGroupListAttributes->target,
        $fieldColumnGroupListAttributes->size,
        $fieldColumnGroupListAttributes->step
      )
      ->assembleFieldMappings($header);
    return true;

  }

  public function map(callable $callback)
  {
    $callback($this);
  }

  public function getGroupedFields(): array
  {
    $result = [];
    foreach ($this->mappings as $fieldMapping) {
      $this->createGroupedColumn($result, $fieldMapping);
    }
    return $result;
  }

  private function createGroupedColumn(
    array &$groupedHeader,
    FieldMapping $fieldMapping,
    int $offset = 0,
    int $step = 0,
    $address = []
  )
  {
    if (isset($fieldMapping->column)) {
      $columnIndex = Coordinate::columnIndexFromString($fieldMapping->column);
      $columnIndex += $offset;
      $column = Coordinate::stringFromColumnIndex($columnIndex);
      $groupedHeader[$column][] = new FieldMetadata($address, $fieldMapping);
    } else if (isset($fieldMapping->groupItem)) {
      foreach ($fieldMapping->groupItem->getMappings() as $mapping) {
        $addressCopy = $address;
        $addressItem = new FieldAddressItem(FieldAddressItem::ASSIGNMENT_SINGLE, $fieldMapping->field, $mapping->entityName);
        $addressCopy[] = $addressItem;
        $this->createGroupedColumn($groupedHeader, $mapping, $offset, $step, $addressCopy);
      }
    } else if (isset($fieldMapping->groupList)) {
      for ($i = 0; $i < $fieldMapping->groupList['params']['size']; $i++) {
        foreach ($fieldMapping->groupList['mappingProvider']->getMappings() as $innerFieldMapping) {
          $addressCopy = $address;
          $newAddress = new FieldAddressItem(
            FieldAddressItem::ASSIGNMENT_MULTIPLE,
            $fieldMapping->field,
            $innerFieldMapping->entityName,
            $i
          );

          $addressCopy[] = $newAddress;
          $this->createGroupedColumn(
            $groupedHeader,
            $innerFieldMapping,
            $offset + ($i * $fieldMapping->groupList['params']['step']),
            $fieldMapping->groupList['params']['step'],
            $addressCopy
          );
        }
      }
    }
  }
}
