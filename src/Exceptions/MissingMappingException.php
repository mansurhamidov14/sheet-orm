<?php

namespace Twelver313\SheetORM\Exceptions;

final class MissingMappingException extends SheetmapException
{
  protected $entityName;

  public function __construct(string $entityName)
  {
    parent::__construct(sprintf("Entity '%s' has no mapping and is not loaded.", $entityName));
    $this->entityName = $entityName;
  }

  public function getEntityName(): string
  {
    return $this->entityName;
  }
}
