<?php

namespace Twelver313\Sheetmap\Attributes;

class AttributeHelpers
{
  public static function attributesSupported()
  {
    return PHP_VERSION_ID >= 80000;
  }
}
