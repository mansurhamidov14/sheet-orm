<?php

namespace Twelver313\Sheetmap;

use Twelver313\Sheetmap\FieldMapping;

class ArrayMapping implements MappingProvider
{
  /** @var FieldMapping[] */
  private $mappings = [];

  /** @var MetadataResolver */
  private $arraySchema;

  public function __construct(MetadataResolver $arraySchema)
  {
    $this->arraySchema = $arraySchema;
  }

  public function field(string $key): FieldMapping {
    return $this->mappings[$key] = new FieldMapping(
      $key,
      $this->arraySchema->getEntityName()
    );
  }

  public function assembleFieldMappings(array $header): self
  {
    foreach ($this->mappings as $mapping) {
      if (empty($mapping->column) && $mapping->title) {
        $mapping->column = $header[$mapping->title] ?? null;
      }
    }

    return $this;
  }

  public function getGroupedFields(): array
  {
    $result = [];
    foreach ($this->mappings as $fieldMapping) {
      if (!isset($fieldMapping->column)) {
        continue;
      }

      $result[$fieldMapping->column][] = $fieldMapping;
    }

    return $result;
  }
}
