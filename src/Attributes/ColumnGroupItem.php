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
  public $target;
  public $repeatTimes;
  public $step;

  public function __construct(string $target)
  {
    $this->target = $target;
  }
}
