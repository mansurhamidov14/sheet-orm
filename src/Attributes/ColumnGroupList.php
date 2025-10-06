<?php

namespace Twelver313\Sheetmap\Attributes;

/**
 * @Annotation
 * @NamedArgumentConstructor
 * @Target({"PROPERTY"})
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
class ColumnGroupList extends ColumnGroupItem
{
  public $size;
  public $step;

  public function __construct(string $target, int $size, int $step)
  {
    parent::__construct($target);
    $this->size = $size;
    $this->step = $step;
  }
}
