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
  public $params;

  public function __construct(string $target, array $params = [])
  {
    $this->target = $target;
    $this->params = $params;
  }
}
