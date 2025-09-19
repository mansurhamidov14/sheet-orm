<?php

namespace Twelver313\Sheetmap;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class Column
{
  public function __construct(public string $type) {}
}