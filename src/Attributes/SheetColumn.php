<?php

namespace Twelver313\Sheetmap\Attributes;

use Twelver313\Sheetmap\ValueFormatter;

/**
 * @Annotation
 * @NamedArgumentConstructor
 * @Target({"PROPERTY"})
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
class SheetColumn
{
  public $title;
  public $letter;
  public $type;

  public function __construct(?string $title = null, ?string $type = null, ?string $letter = null) {
    $this->title = $title;
    $this->type = $type ?? ValueFormatter::TYPE_AUTO;
    $this->letter = $letter;
  }
}
