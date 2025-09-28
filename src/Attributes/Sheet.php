<?php

namespace Twelver313\Sheetmap\Attributes;

use Twelver313\Sheetmap\SheetConfigInterface;

/**
 * @Annotation
 * @NamedArgumentConstructor
 * @Target({"CLASS"})
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
class Sheet implements SheetConfigInterface
{
  public $name;
  public $index;
  public $startRow;
  public $endRow;

  public function __construct(?string $name = null, ?int $index = null, int $startRow = 2, ?int $endRow = null) {
    $this->name = $name;
    $this->index = null;
    $this->startRow = $startRow ?? 2;
    $this->endRow = $endRow;
  }
}
