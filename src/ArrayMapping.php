<?php

namespace Twelver313\Sheetmap;

use Twelver313\Sheetmap\KeyMapping;

class ArrayMapping
{
  /** @var KeyMapping[] */
  private $mappings = [];

  public function key(string $key): KeyMapping {
    return $this->mappings[$key] = new KeyMapping(
      $key,
    );
  }

  public function getMappings(): array {
    return $this->mappings;
  }

  public function setMappings($key, $mapping) {
    $this->mappings[$key] = $mapping;
  }

  public function linkHeaderTitlesToLetters(array $header): self
  {
    foreach ($this->mappings as $mapping) {
      if (empty($mapping->column) && $mapping->title) {
        $mapping->column = $header[$mapping->title] ?? null;
      }
    }

    return $this;
  }

  public function getGroupedKeys()
  {
    $result = [];
    foreach ($this->mappings as $keyMapping) {
      if (!isset($keyMapping->column)) {
        continue;
      }

      $result[$keyMapping->column][] = $keyMapping;
    }

    return $result;
  }
}
