<?php

namespace Twelver313\SheetORM\Exceptions;

use Twelver313\SheetORM\Validation\SheetValidationContext;

final class InvalidSheetTemplateException extends SheetmapException
{
  /** @var array */
  protected $params;

  /** @var SheetValidationContext */
  protected $context;

  public function __construct(string $message, array $params, SheetValidationContext $context)
  {
    parent::__construct($message);
    $this->params = $params;
    $this->context = $context;
  }

  public function getParams()
  {
    return $this->params;
  }

  public function getContext()
  {
    return $this->context;
  }
}
