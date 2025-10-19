<?php /** @noinspection ALL */

namespace Twelver313\SheetORM\Attributes;

/**
 * @Annotation
 * @NamedArgumentConstructor
 * @Target({"ANNOTATION", "CLASS"})
 */
#[\Attribute(\Attribute::TARGET_CLASS|\Attribute::IS_REPEATABLE)]
class TitleRow
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
