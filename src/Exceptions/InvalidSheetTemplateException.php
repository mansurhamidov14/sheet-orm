<?php

namespace Twelver313\Sheetmap\Exceptions;

use Twelver313\Sheetmap\Validation\ValidationContext;

final class InvalidSheetTemplateException extends SheetmapException
{
  /** @var array */
  protected $params;

  /** @var ValidationContext */
  protected $context;

  public function __construct(string $message, array $params, ValidationContext $context)
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
