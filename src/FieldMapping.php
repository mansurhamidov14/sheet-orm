<?php

namespace Twelver313\Sheetmap;

use Twelver313\Sheetmap\ModelMapping;

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

  public function groupItem(string $target)
  {
    $metadataResolver = new ModelMetadata($target);
    $this->groupItem = new ModelMapping($metadataResolver);
    return $this->groupItem;
  }

  public function groupList(string $target, int $size, int $step)
  {
    $metadataResolver = new ModelMetadata($target);
    $this->groupList = [
      'params' => ['size' => $size, 'step' => $step],
      'mappingProvider' => new ModelMapping($metadataResolver)
    ];

    return $this->groupList['mappingProvider'];
  }
}
