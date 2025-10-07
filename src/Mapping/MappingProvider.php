<?php

namespace Twelver313\Sheetmap\Mapping;

use Twelver313\Sheetmap\Field\FieldAddressItem;
use Twelver313\Sheetmap\Field\FieldMapping;
use Twelver313\Sheetmap\Field\FieldMetadata;
use Twelver313\Sheetmap\Helpers\Calculations;
use Twelver313\Sheetmap\MetadataResolver;

abstract class MappingProvider
{
  /** @var FieldMapping[] */
  protected $mappings = [];

  /** @var ModelMetadata */
  protected $metadataResolver;

  public function __construct(MetadataResolver $metadataResolver)
  {
    $this->metadataResolver = $metadataResolver;
  }

  abstract public function assembleFieldMappings(array $header): self;

  public function field(string $field): FieldMapping
  {
    return $this->mappings[$field] = new FieldMapping(
      $field,
      $this->metadataResolver->getEntityName()
    );
  }

  public function getMappings()
  {
    return $this->mappings;
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
      $column = Calculations::getNextColumn($fieldMapping->column, $offset);
      $groupedHeader[$column][] = new FieldMetadata($address, $fieldMapping);
    } else if (isset($fieldMapping->groupItem)) {
      $this->handleGroupItem($fieldMapping, $groupedHeader, $address, $offset, $step);
    } else if (isset($fieldMapping->groupList)) {
      $this->handleGroupList($fieldMapping, $groupedHeader, $address, $offset, $step);
    }
  }

  private function handleGroupItem(FieldMapping $fieldMapping, array &$groupedHeader, array $address, int $offset, int $step)
  {
    foreach ($fieldMapping->groupItem->getMappings() as $mapping) {
      $addressItem = new FieldAddressItem(FieldAddressItem::ASSIGNMENT_SINGLE, $fieldMapping->field, $mapping->entityName);
      $address[] = $addressItem;
      $this->createGroupedColumn($groupedHeader, $mapping, $offset, $step, $address);
    }
  }

  private function handleGroupList(FieldMapping $fieldMapping, array &$groupedHeader, array $address, int $offset, int $step)
  {
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
          Calculations::getNextOffset($offset, $fieldMapping->groupList['params']['step'], $i),
          $fieldMapping->groupList['params']['step'],
          $addressCopy
        );
      }
    }
  }

  public function map(callable $callback)
  {
    $callback($this);
  }
}