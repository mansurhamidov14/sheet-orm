<?php

namespace Twelver313\Sheetmap\Validation;

class HeaderValidationFlags
{
  const IGNORE_CASE = 1 << 0;
  const REGEXP = 1 << 1;
  const EXACT_COLUMNS = 1 << 2;
  const IGNORE_ORDER = 1 << 3;

  public static function hasFlag(int $needle, int $haystack): bool
  {
    return $haystack & $needle;
  }
}