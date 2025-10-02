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
  public function __construct(?string $name = null, ?int $index = null, int $headerRow = 2, int $startRow = 2, ?int $endRow = null) {
    $this->name = $name;
    $this->index = $index ?? null;
    $this->headerRow = $headerRow;
    $this->startRow = $startRow ?? 2;
    $this->endRow = $endRow;
  }
}
