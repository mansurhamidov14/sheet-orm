<?php

namespace Twelver313\Sheetmap\Mapping;

class ArrayMapping extends MappingProvider
{
  public function assembleFieldMappings(array $header): MappingProvider
  {
    foreach ($this->mappings as $mapping) {
      if (empty($mapping->column) && $mapping->title) {
        $mapping->column = $header[$mapping->title] ?? null;
      }

      if (isset($mapping->groupItem)) {
        $mapping->groupItem->assembleFieldMappings($header);
      }

      if (isset($mapping->groupList)) {
        $mapping->groupList['mappingProvider']->assembleFieldMappings($header);
      }
    }

    return $this;
  }
}
