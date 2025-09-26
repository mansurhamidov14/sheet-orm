<?php

namespace Twelver313\Sheetmap\Exceptions;

final class MissingClassMetadataException extends SheetmapException
{
  protected $className;

  public function __construct(string $className)
  {
    parent::__construct(sprintf("Class '%s' has no metada and is not loaded.", $className));
    $this->className = $className;
  }

  public function getClassName()
  {
    return $this->className;
  }
}
