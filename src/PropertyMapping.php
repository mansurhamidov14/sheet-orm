<?php

namespace Twelver313\Sheetmap;

class PropertyMapping
{
  public ?string $property = null;
  public ?string $title = null;
  public ?string $column = null;
  public ?string $type = null;

  public function __construct(string $property)
  {
    $this->property = $property;
  }

  public function title(string $title): self
  {
    $this->title = $title;
    return $this;
  }

  public function column(string $column): self
  {
    $this->column = $column;
    return $this;
  }

  public function type(string $type): self {
    $this->type = $type;
    return $this;
  }
}
