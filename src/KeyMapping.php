<?php

namespace Twelver313\Sheetmap;

class KeyMapping
{
  public $key;
  public $title = null;
  public $column = null;
  public $type = ValueFormatter::TYPE_AUTO;

  public function __construct(string $key)
  {
    $this->key = $key;
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
