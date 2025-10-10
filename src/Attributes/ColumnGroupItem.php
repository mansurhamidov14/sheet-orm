<?php

namespace Twelver313\Sheetmap\Attributes;

/**
 * @Annotation
 * @NamedArgumentConstructor
 * @Target({"PROPERTY"})
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
class ColumnGroupItem
{
  /** @var string $target */
  public $target;

  public function __construct(string $target)
  {
    $this->target = $target;
  }
}
