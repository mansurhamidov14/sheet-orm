<?php

namespace Twelver313\Sheetmap\Attributes;

use Twelver313\Sheetmap\SheetConfig;

/**
 * @Annotation
 * @NamedArgumentConstructor
 * @Target({"CLASS"})
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
class Sheet extends SheetConfig
{
  public function __construct(?string $name = null, ?int $index = null, ?int $startRow = null, ?int $endRow = null) {
    $this->name = $name;
    $this->index = $index ?? null;
    $this->startRow = $startRow ?? null;
    $this->endRow = $endRow;
  }
}
