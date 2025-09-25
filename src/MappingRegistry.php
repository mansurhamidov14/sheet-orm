<?php

namespace Twelver313\Sheetmap;

use Twelver313\Sheetmap\ClassMapping;
use Twelver313\Sheetmap\Exceptions\MissingClassMappingException;
use Twelver313\Sheetmap\MetadataResolver;

class MappingRegistry
{
  /** @var */
  private $mappings;

  public function __construct()
  {
    $this->mappings = [];
  }

  public function register(MetadataResolver $metadataResolver): ClassMapping
  {
    $className = $metadataResolver->getClass();
    $this->mappings[$className] = new ClassMapping($metadataResolver);
    return $this->mappings[$className];
  }

  public function registerMissing(MetadataResolver $metadataResolver)
  {
    $className = $metadataResolver->getClass();

    try {
      return $this->get($className);
    } catch (MissingClassMappingException $e) {
      return $this->register($metadataResolver);
    }
  }

  public function exists(string $className)
  {
    return isset($this->mappings[$className]);
  }

  public function get(string $className): ClassMapping {
    if (!isset($this->mappings[$className])) {
      throw new MissingClassMappingException($className);
    }

    return $this->mappings[$className];
  }
}