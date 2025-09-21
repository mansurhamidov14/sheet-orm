<?php

namespace Twelver313\Sheetmap;

#[\Attribute(\Attribute::TARGET_CLASS)]
class Sheet
{
  public function __construct(public ?string $sheet = null, public ?int $sheetIndex = 0) {}
}