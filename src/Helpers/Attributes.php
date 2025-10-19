<?php

namespace Twelver313\SheetORM\Helpers;

class Attributes
{
  public static function attributesSupported(): bool
  {
    return PHP_VERSION_ID >= 80000;
  }
}
