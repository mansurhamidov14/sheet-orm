<?php

namespace Twelver313\Sheetmap;

#[\Attribute(\Attribute::TARGET_CLASS)]
class Sheet
{
  public function __construct(
    public ?string $name = null,
    public ?int $index = null,
    public int $startRow = 2,
    public ?int $endRow = null
  ) {}
}