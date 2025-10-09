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
  /** @var ModelMapping */
  public $groupItem = null;
  public $groupList = null;

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
  public function groupItem($target, array $config = []): MappingProvider
  {
    if ($target instanceof ArraySchema) {
      $this->groupItem = $target->getMapping();
      return $this->groupItem;
    }

    if (!class_exists($target)) {
      $arraySchema = new ArraySchema($target, $config);
      $this->groupItem = $arraySchema->getMapping();
      return $this->groupItem;
    }

    $metadataResolver = new ModelMetadata($target);
    $this->groupItem = new ModelMapping($metadataResolver);
    return $this->groupItem;
  }

  /**
   * @param string|ArraySchema $target
   */
  public function groupList($target, int $size, int $step, array $config = []): MappingProvider
  {
    $params = ['size' => $size, 'step' => $step];
    
    if ($target instanceof ArraySchema) {
      $this->groupList = [
        'params' => $params,
        'mappingProvider' => $target->getMapping()
      ];
      return $target->getMapping();
    }

    if (!class_exists($target)) {
      $arraySchema = new ArraySchema($target, $config);
      $this->groupList = [
        'params' => $params,
        'mappingProvider' => $arraySchema->getMapping()
      ];
      return $arraySchema->getMapping();
    }
    
    $metadataResolver = new ModelMetadata($target);
    
    $this->groupList = [
      'params' => $params,
      'mappingProvider' => new ModelMapping($metadataResolver)
    ];

    return $this->groupList['mappingProvider'];
  }
}
