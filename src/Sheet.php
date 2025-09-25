<?php

namespace Twelver313\Sheetmap;

use Twelver313\Sheetmap\SheetConfigInterface;

#[\Attribute(\Attribute::TARGET_CLASS)]
class Sheet implements SheetConfigInterface
{
  public function __construct(
    public ?string $name = null,
    public ?int $index = null,
    public int $startRow = 2,
    public ?int $endRow = null
  ) {}
}