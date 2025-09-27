<?php

namespace Twelver313\Sheetmap;

use Twelver313\Sheetmap\ModelMapping;
use Twelver313\Sheetmap\Exceptions\MissingModelMappingException;
use Twelver313\Sheetmap\MetadataResolver;

class MappingRegistry
{
  /** @var */
  private $mappings;

  public function __construct()
  {
    $this->mappings = [];
  }

  public function register(MetadataResolver $metadataResolver): ModelMapping
  {
    $modelName = $metadataResolver->getModel();
    $this->mappings[$modelName] = new ModelMapping($metadataResolver);
    return $this->mappings[$modelName];
  }

  public function registerMissing(MetadataResolver $metadataResolver)
  {
    $modelName = $metadataResolver->getModel();

    try {
      return $this->get($modelName);
    } catch (MissingModelMappingException $e) {
      return $this->register($metadataResolver);
    }
  }

  public function exists(string $modelName)
  {
    return isset($this->mappings[$modelName]);
  }

  public function get(string $modelName): ModelMapping {
    if (!isset($this->mappings[$modelName])) {
      throw new MissingModelMappingException($modelName);
    }

    return $this->mappings[$modelName];
  }
}
