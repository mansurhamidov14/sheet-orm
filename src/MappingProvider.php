<?php

namespace Twelver313\Sheetmap;

interface MappingProvider
{
  public function field(string $field): FieldMapping;
  public function assembleFieldMappings(array $header): self;
  public function getGroupedFields(): array;
}