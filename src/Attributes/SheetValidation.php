<?php

namespace Twelver313\SheetORM\Attributes;

use Twelver313\SheetORM\Validation\SheetValidationStrategy;

/**
 * @Annotation
 * @NamedArgumentConstructor
 * @Target({"CLASS"})
 */
#[\Attribute(\Attribute::TARGET_CLASS|\Attribute::IS_REPEATABLE)]
class SheetValidation
{
  public $strategy;
  public $params;
  public $message;

  public function __construct(string $strategy, ?array $params = null, ?string $message = null)
  {
    $this->strategy = $strategy;
    $this->params = $params;
    $this->message = $message;
  }

  public function getStrategyInstance(): SheetValidationStrategy
  {
    return new $this->strategy();
  }
}
