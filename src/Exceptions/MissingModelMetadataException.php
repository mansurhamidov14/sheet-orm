<?php

namespace Twelver313\Sheetmap\Exceptions;

final class MissingModelMetadataException extends SheetmapException
{
  protected $modelName;

  public function __construct(string $modelName)
  {
    parent::__construct(sprintf("Class '%s' has no metada and is not loaded.", $modelName));
    $this->modelName = $modelName;
  }

  public function getModelName()
  {
    return $this->modelName;
  }
}
