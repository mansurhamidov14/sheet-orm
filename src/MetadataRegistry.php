<?php

namespace Twelver313\SheetORM;

use Twelver313\SheetORM\Exceptions\MissingMetadataException;

class MetadataRegistry
{
  /** @var MetadataResolver[] */
  private $registry = [];

  public function exists(string $entityName): bool
  {
    return isset($this->registry[$entityName]);
  }

  public function register(MetadataResolver $metadataResolver): void
  {
    $this->registry[$metadataResolver->getEntityName()] = $metadataResolver;
  }

  public function get(string $entityName): MetadataResolver
  {
    if (!$this->exists($entityName)) {
      throw new MissingMetadataException($entityName);
    }
    return $this->registry[$entityName];
  }
}
