<?php

namespace Twelver313\Sheetmap;

class FieldMapping
{
  public $entityName;
  public $field;
  public $title = null;
  public $column = null;
  public $type = null;

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
}
