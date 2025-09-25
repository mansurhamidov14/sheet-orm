<?php

namespace Twelver313\Sheetmap\Exceptions;

final class MissingClassMappingException extends SheetmapException
{
  protected $className;

  public function __construct(string $className)
  {
    parent::__construct(sprintf("Class '%s' has no mapping and is not loaded.", $className));
    $this->className = $className;
  }

  public function getClassName(): string
  {
    return $this->className;
  }
}
