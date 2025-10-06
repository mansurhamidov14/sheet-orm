<?php

namespace Twelver313\Sheetmap;

use Twelver313\Sheetmap\FieldAddressItem;
use Twelver313\Sheetmap\FieldMapping;

class FieldMetadata
{
  /** @var FieldAddressItem[] */
  public $address;

  /** @var FieldMapping */
  public $mapping;

  /**
   * @param FieldAddressItem[] $address
   * @param FieldMapping $mapping
   */
  public function __construct(array $address, FieldMapping $mapping)
  {
    $this->address = $address;
    $this->mapping = $mapping;
  }

  public function isRootField(): bool
  {
    return empty($this->address);
  }
}