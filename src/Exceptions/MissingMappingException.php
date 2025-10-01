<?php

namespace Twelver313\Sheetmap\Exceptions;

final class MissingMappingException extends SheetmapException
{
  protected $entityName;

  public function __construct(string $entityName)
  {
    parent::__construct(sprintf("Entity '%s' has no model mapping and is not loaded.", $entityName));
    $this->entityName = $entityName;
  }

  public function getEntityName(): string
  {
    return $this->entityName;
  }
}
