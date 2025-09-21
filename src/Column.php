<?php

namespace Twelver313\Sheetmap;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class Column
{
  public function __construct(
    public ?string $title = null,
    public ?string $type = null,
    public ?string $letter = null
  ) {}
}