<?php

namespace Twelver313\SheetORM\Attributes;

/**
 * @Annotation
 * @Target({"CLASS"})
 */
class SheetValidators
{
  /**
   * @var SheetValidation[]
   * @Annotation\Required()
   */
  public $value = [];

  public function __construct(array $data)
  {
    if (isset($data['value']) && $data['value'] instanceof SheetValidation) {
      $data['value'] = [$data['value']];
    }

    $this->value = $data['value'];
  }
}
