<?php

namespace Twelver313\Sheetmap\Field;

use Twelver313\Sheetmap\ArraySchema;
use Twelver313\Sheetmap\Mapping\MappingProvider;
use Twelver313\Sheetmap\Mapping\ModelMapping;
use Twelver313\Sheetmap\ModelMetadata;

class FieldMapping
{
  public $entityName;
  public $field;
  public $title = null;
  public $column = null;
  public $type = null;
  /** @var ColumnGroup */
  public $columnGroup = null;

  public function __construct(string $field, string $entityName)
  {
    $this->field = $field;
    $this->entityName = $entityName;
  }

  public function title(string $title): self
  {
    $this->title = $title;
    return $this;
  }

  public function column(?string $column): self
  {
    $this->column = $column;
    return $this;
  }

  public function type(string $type): self {
    $this->type = $type;
    return $this;
  }

  /**
   * @param string|ArraySchema $target
   */
  public function groupItem($target, array $params = []): MappingProvider
  {
    if ($target instanceof ArraySchema) {
      $this->columnGroup = new ColumnGroup($target->getMapping(), $params);
      return $this->columnGroup->getMappingProvider();
    }

    if (!class_exists($target)) {
      $arraySchema = new ArraySchema($target, $params);
      $this->columnGroup = new ColumnGroup($arraySchema->getMapping(), $params);
      return $this->columnGroup->getMappingProvider();
    }

    $metadataResolver = new ModelMetadata($target);
    $mappingProvider = new ModelMapping($metadataResolver);
    $this->columnGroup = new ColumnGroup($mappingProvider, $params);
    return $this->columnGroup->getMappingProvider();
  }

  /**
   * @param string|ArraySchema $target
   */
  public function groupList($target, array $params): MappingProvider
  {
    if ($target instanceof ArraySchema) {
      $this->columnGroup = new ColumnGroup($target->getMapping(), $params, true);
      return $this->columnGroup->getMappingProvider();
    }

    if (!class_exists($target)) {
      $arraySchema = new ArraySchema($target);
      $this->columnGroup = new ColumnGroup($arraySchema->getMapping(), $params, true);
      return $this->columnGroup->getMappingProvider();
    }
    
    $metadataResolver = new ModelMetadata($target);
    $this->columnGroup = new ColumnGroup(new ModelMapping($metadataResolver), $params, true);
    return $this->columnGroup->getMappingProvider();
  }
}
