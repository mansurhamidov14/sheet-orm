<?php

namespace Twelver313\Sheetmap;

use Twelver313\Sheetmap\PropertyMapping;

class ClassMapping
{
  /** @var PropertyMapping[] */
  private array $mappings = [];

  public function property(string $property): PropertyMapping {
    return $this->mappings[$property] = new PropertyMapping($property);
  }

  public function getMappings(): array {
    return $this->mappings;
  }

  public function setMappings($property, $mapping) {
    $this->mappings[$property] = $mapping;
  }
}
