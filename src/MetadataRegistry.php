<?php

namespace Twelver313\Sheetmap;

use Twelver313\Sheetmap\Exceptions\MissingClassMetadataException;
use Twelver313\Sheetmap\MetadataResolver;

class MetadataRegistry
{
  /** @var MetadataResolver[] */
  private $registry = [];

  public function exists(string $className)
  {
    return isset($this->registry[$className]);
  }

  public function register(string $className): MetadataResolver
  {
    try {
      return $this->get($className);
    } catch (MissingClassMetadataException $e) {
      $this->registry[$className] = new MetadataResolver($className);
      return $this->get($className);
    }
  }

  public function get(string $className): MetadataResolver
  {
    if (!$this->exists($className)) {
      throw new MissingClassMetadataException($className);
    }
    return $this->registry[$className];
  }
}