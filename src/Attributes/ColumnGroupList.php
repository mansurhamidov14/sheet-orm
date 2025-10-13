<?php

namespace Twelver313\SheetORM\Attributes;

use Twelver313\SheetORM\Exceptions\SheetORMException;

/**
 * @Annotation
 * @NamedArgumentConstructor
 * @Target({"PROPERTY"})
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
class ColumnGroupList extends ColumnGroup
{
  /** @var int */
  public $size;
  /** @var int */
  public $step;

  /**
   * @param int $size Repeat times i.e. length of derived array
   * @param int $step Amount of columns in sheet ocuppied by a single group item
   */
  public function __construct(string $target, int $size, int $step)
  {
    if ($size < 1) {
      throw new SheetORMException('"size" parameter must be a positive integer for column group list');
    }

    if ($step < 1) {
      throw new SheetORMException('"step" parameter must be a positive integer for column group list');
    }

    parent::__construct($target);
    $this->size = $size;
    $this->step = $step;
  }
}
