<?php

namespace Twelver313\SheetORM\Attributes;

/**
 * @Annotation
 * @Target({"CLASS"})
 */
class TitleRows
{
  /**
   * @var TitleRow[]
   * @Annotation\Required()
   */
  public $value = [];

  public function __construct(array $data)
  {
    // Normalize if a single @TitleRow was passed
    if (isset($data['value']) && $data['value'] instanceof TitleRow) {
      $data['value'] = [$data['value']];
    }

    $this->value = $data['value'];
  }
}
