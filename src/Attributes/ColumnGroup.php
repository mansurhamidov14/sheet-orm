<?php

namespace Twelver313\SheetORM\Attributes;

/**
 * @Annotation
 * @NamedArgumentConstructor
 * @Target({"PROPERTY"})
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
class ColumnGroup
{
  /** @var string $target */
  public $target;

  public function __construct(string $target)
  {
    $this->target = $target;
  }
}
