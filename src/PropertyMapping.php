<?php

namespace Twelver313\Sheetmap;

class PropertyMapping
{
  public string $className;
  public string $property;
  public string|null $title = null;
  public string|null $column = null;
  public string|null $type = null;

  public function __construct(string $property, string $className)
  {
    $this->property = $property;
    $this->className = $className;
  }

  public function title(string $title): self
  {
    $this->title = $title;
    return $this;
  }

  public function column(string|null $column): self
  {
    $this->column = $column;
    return $this;
  }

  public function type(string $type): self {
    $this->type = $type;
    return $this;
  }
}
