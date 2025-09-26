<?php

namespace Twelver313\Sheetmap;

use Twelver313\Sheetmap\Validation\ValidationStrategy;

#[\Attribute(\Attribute::TARGET_CLASS|\Attribute::IS_REPEATABLE)]
class Validation
{
  public $strategy;
  public $params;
  public $message;

  public function __construct(string $strategy, array|null $params = null, string|null $message = null)
  {
    $this->strategy = $strategy;
    $this->params = $params;
    $this->message = $message;
  }

  public function getStrategyInstance(): ValidationStrategy
  {
    return new ($this->strategy)();
  }
}
