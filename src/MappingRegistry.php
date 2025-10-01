<?php

namespace Twelver313\Sheetmap;

use Twelver313\Sheetmap\ArrayMapping;
use Twelver313\Sheetmap\Exceptions\MissingMappingException;
use Twelver313\Sheetmap\ModelMapping;

class MappingRegistry
{
  /** @var */
  private $mappings;

  public function __construct()
  {
    $this->mappings = [];
  }

  public function register(string $name, ModelMapping|ArrayMapping $mapping): void
  {
    $this->mappings[$name] = $mapping;
  }

  public function registerMissing(string $name, ModelMapping|ArrayMapping $mapping): void
  {
    if (!$this->exists($name)) {
      $this->register($name, $mapping);
    }
  }

  public function exists(string $entityName)
  {
    return isset($this->mappings[$entityName]);
  }

  public function get(string $entityName): ModelMapping|ArrayMapping
  {
    if (!isset($this->mappings[$entityName])) {
      throw new MissingMappingException($entityName);
    }

    return $this->mappings[$entityName];
  }
}
