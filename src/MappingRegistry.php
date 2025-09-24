<?php

namespace Twelver313\Sheetmap;

use Twelver313\Sheetmap\ClassMapping;
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
    $class = $metadataResolver->getClass();
    $this->mappings[$class] = new ClassMapping($metadataResolver);
    return $this->mappings[$class];
  }

  public function registerMissing(MetadataResolver $metadataResolver)
  {
    $className = $metadataResolver->getClass();

    try {
      return $this->get($className);
    } catch (\Exception $e) {
      return $this->register($metadataResolver);
    }
  }

  public function exists(string $class)
  {
    return isset($this->mappings[$class]);
  }

  public function get(string $class): ClassMapping {
    if (!isset($this->mappings[$class])) {
      throw new \Exception("Class '{$class}' was not loaded. Please make sure that you are not using this out of context");
    }

    return $this->mappings[$class];
  }
}