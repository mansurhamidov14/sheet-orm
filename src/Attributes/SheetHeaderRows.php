<?php

namespace Twelver313\SheetORM\Attributes;

/**
 * @Annotation
 * @Target({"CLASS"})
 */
class SheetHeaderRows
{
  /**
   * @var SheetHeaderRow[]
   * @Annotation\Required()
   */
  public $value = [];

  public function __construct(array $data)
  {
    // Normalize if a single @SheetHeaderRow was passed
    if (isset($data['value']) && $data['value'] instanceof SheetHeaderRow) {
      $data['value'] = [$data['value']];
    }

    $this->value = $data['value'];
  }
}
