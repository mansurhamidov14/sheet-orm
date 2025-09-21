<?php

namespace Twelver313\Sheetmap;

class PropertyMapping
{
  public ?string $property = null;
  public ?string $column = null;
  public ?string $columnLetter = null;
  public ?string $type = null;

  public function __construct(string $property)
  {
    $this->property = $property;
  }

  public function columnLetter(string $columnLetter): self
  {
    $this->columnLetter = $columnLetter;
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
