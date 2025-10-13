<?php

namespace Twelver313\SheetORM\Exceptions;

final class MissingMetadataException extends SheetORMException
{
  protected $entityName;

  public function __construct(string $entityName)
  {
    parent::__construct(sprintf("Entity '%s' has no metadata and is not loaded.", $entityName));
    $this->entityName = $entityName;
  }

  public function getEntityName()
  {
    return $this->entityName;
  }
}
