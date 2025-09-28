<?php

namespace Twelver313\Sheetmap;

class PropertyMapping
{
  public $modelName;
  public $property;
  public $title = null;
  public $column = null;
  public $type = null;

  public function __construct(string $property, string $modelName)
  {
    $this->property = $property;
    $this->modelName = $modelName;
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
}
