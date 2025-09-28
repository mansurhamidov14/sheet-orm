<?php

namespace Twelver313\Sheetmap;

use Twelver313\Sheetmap\ValueFormatter;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class SheetColumn
{
  public function __construct(
    public ?string $title = null,
    public ?string $type = ValueFormatter::TYPE_AUTO,
    public ?string $letter = null
  ) {}
}
