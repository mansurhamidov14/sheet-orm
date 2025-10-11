<?php

namespace Twelver313\SheetORM\Field;

class FieldAddressItem
{
  const ASSIGNMENT_SINGLE = 'single';
  const ASSIGNMENT_MULTIPLE = 'multiple';

  public $assignmentType;
  public $fieldName;
  public $target;
  public $index;

  /**
   * @param string $fieldType One of TYPE_* constants
   * @param string|int $value Field name or index value
   * @param string $target Target class name for object property, empty for array index
   */
  public function __construct(string $assignmentType, string $fieldName, string $target, ?int $index = null)
  {
    $this->assignmentType = $assignmentType;
    $this->fieldName = $fieldName;
    $this->target = $target;
    $this->index = $index;
  }

  public function isArrayItem()
  {
    return $this->assignmentType === self::ASSIGNMENT_MULTIPLE;
  }
}