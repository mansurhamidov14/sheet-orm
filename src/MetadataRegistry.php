<?php

namespace Twelver313\Sheetmap;

use Twelver313\Sheetmap\Exceptions\MissingModelMetadataException;
use Twelver313\Sheetmap\MetadataResolver;

class MetadataRegistry
{
  /** @var MetadataResolver[] */
  private $registry = [];

  public function exists(string $modelName)
  {
    return isset($this->registry[$modelName]);
  }

  public function register(string $modelName): MetadataResolver
  {
    try {
      return $this->get($modelName);
    } catch (MissingModelMetadataException $e) {
      $this->registry[$modelName] = new MetadataResolver($modelName);
      return $this->get($modelName);
    }
  }

  public function get(string $modelName): MetadataResolver
  {
    if (!$this->exists($modelName)) {
      throw new MissingModelMetadataException($modelName);
    }
    return $this->registry[$modelName];
  }
}
