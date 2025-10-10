<?php

namespace Twelver313\Sheetmap\Mapping;

use Twelver313\Sheetmap\Field\FieldAddressItem;
use Twelver313\Sheetmap\Field\FieldMapping;
use Twelver313\Sheetmap\Field\FieldMetadata;
use Twelver313\Sheetmap\Helpers\Calculations;
use Twelver313\Sheetmap\MetadataResolver;
use Twelver313\Sheetmap\SheetHeader;

abstract class MappingProvider
{
  /** @var FieldMapping[] */
  protected $mappings = [];

  /** @var \Twelver313\Sheetmap\ModelMetadata */
  protected $metadataResolver;

  public function __construct(MetadataResolver $metadataResolver)
  {
    $this->metadataResolver = $metadataResolver;
  }

  abstract public function assembleFieldMappings(SheetHeader $header): self;

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
      $column = Calculations::getShiftedColumn($fieldMapping->column, $offset);
      $groupedHeader[$column][] = new FieldMetadata($address, $fieldMapping);
    } else if (isset($fieldMapping->columnGroup)) {
      $this->handleGroup($fieldMapping, $groupedHeader, $address, $offset, $step);
    }
  }

  private function handleGroup(FieldMapping $fieldMapping, array &$groupedHeader, array $address, int $offset, int $step)
  {
    if (!isset($fieldMapping->columnGroup)) {
      return;
    }
    if ($fieldMapping->columnGroup->isList()) {
      $this->handleGroupList($fieldMapping, $groupedHeader, $address, $offset, $step);
      return;
    } else {
      $this->handleGroupItem($fieldMapping, $groupedHeader, $address, $offset, $step);
      return;
    }
  }

  private function handleGroupItem(FieldMapping $fieldMapping, array &$groupedHeader, array $address, int $offset, int $step)
  {

    foreach ($fieldMapping->columnGroup->getMappingProvider()->getMappings() as $mapping) {
      $addressCopy = $address;
      $addressItem = new FieldAddressItem(FieldAddressItem::ASSIGNMENT_SINGLE, $fieldMapping->field, $mapping->entityName);
      $addressCopy[] = $addressItem;
      $this->createGroupedColumn($groupedHeader, $mapping, $offset, $step, $addressCopy);
    }
  }

  private function handleGroupList(FieldMapping $fieldMapping, array &$groupedHeader, array $address, int $offset, int $step)
  {
    for ($i = 0; $i < $fieldMapping->columnGroup->getParams()['size']; $i++) {
      foreach ($fieldMapping->columnGroup->getMappingProvider()->getMappings() as $innerFieldMapping) {
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
          Calculations::getNextOffset($offset, $fieldMapping->columnGroup->getParams()['step'], $i),
          $fieldMapping->columnGroup->getParams()['step'],
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