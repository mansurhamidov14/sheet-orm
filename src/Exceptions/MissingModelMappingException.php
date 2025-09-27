<?php

namespace Twelver313\Sheetmap\Exceptions;

final class MissingModelMappingException extends SheetmapException
{
  protected $modelName;

  public function __construct(string $modelName)
  {
    parent::__construct(sprintf("Class '%s' has no model mapping and is not loaded.", $modelName));
    $this->modelName = $modelName;
  }

  public function getModelName(): string
  {
    return $this->modelName;
  }
}
