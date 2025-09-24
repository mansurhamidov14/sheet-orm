<?php

namespace Twelver313\Sheetmap;

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
    } catch (\Exception $e) {
      $this->registry[$className] = new MetadataResolver($className);
      return $this->get($className);
    }
  }

  public function get(string $className): MetadataResolver
  {
    if (!$this->exists($className)) {
      throw new \Exception("Metadata for {$className} doesn't exist. Please make sure that you are invoking this method correctly");
    }
    return $this->registry[$className];
  }
}