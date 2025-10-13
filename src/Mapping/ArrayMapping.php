<?php

namespace Twelver313\SheetORM\Mapping;

use Twelver313\SheetORM\SheetHeader;

class ArrayMapping extends MappingProvider
{
  public function assembleFieldMappings(SheetHeader $header): MappingProvider
  {
    foreach ($this->mappings as $mapping) {
      $headerRow = $header->getTitleRow($this->metadataResolver->getEntityName(), true);
      if (empty($mapping->column) && $mapping->title) {
        $mapping->column = $headerRow[$mapping->title] ?? null;
      }

      if (isset($mapping->columnGroup)) {
        $mapping->columnGroup->getMappingProvider()->assembleFieldMappings($header);
      }

      if (isset($mapping->groupList)) {
        $mapping->columnGroup->getMappingProvider()->assembleFieldMappings($header);
      }
    }

    return $this;
  }
}
