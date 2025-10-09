<?php

namespace Twelver313\Sheetmap\Attributes;

use Twelver313\Sheetmap\Exceptions\SheetmapException;

/**
 * @Annotation
 * @NamedArgumentConstructor
 * @Target({"PROPERTY"})
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
class ColumnGroupList extends ColumnGroupItem
{

  public function __construct(string $target, array $params)
  {
    if (!isset($params['size'])) {
      throw new SheetmapException('"size" parameter is required for column group list');
    }

    if (!isset($params['step'])) {
      throw new SheetmapException('"step" parameter is required for column group list');
    }

    if (!is_int($params['size']) || $params['size'] < 1) {
      throw new SheetmapException('"size" parameter must be a positive integer for column group list');
    }

    if (!is_int($params['step']) || $params['step'] < 1) {
      throw new SheetmapException('"step" parameter must be a positive integer for column group list');
    }

    parent::__construct($target, $params);
  }
}
