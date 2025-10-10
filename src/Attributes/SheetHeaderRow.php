<?php

namespace Twelver313\Sheetmap\Attributes;

/**
 * @Annotation
 * @NamedArgumentConstructor
 * @Target({"CLASS"})
 */
#[\Attribute(\Attribute::TARGET_CLASS|\Attribute::IS_REPEATABLE)]
class SheetHeaderRow
{
  /** @var string|null */
  public $scope;
  /** @var int */
  public $row;

  public function __construct(?string $scope = null, int $row = 1)
  {
    $this->row = $row;
    $this->scope = $scope;
  }
}
