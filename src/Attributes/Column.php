<?php /** @noinspection ALL */

namespace Twelver313\SheetORM\Attributes;

use Twelver313\SheetORM\Formatter;

/**
 * @Annotation
 * @NamedArgumentConstructor
 * @Target({"PROPERTY"})
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
class Column
{
  public $title;
  public $letter;
  public $type;
  public $params;

  public function __construct(?string $title = null, ?string $type = null, ?string $letter = null, array $params = []) {
    $this->title = $title;
    $this->type = $type ?? Formatter::TYPE_AUTO;
    $this->letter = $letter;
    $this->params = $params;
  }
}
