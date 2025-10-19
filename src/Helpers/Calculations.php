<?php

namespace Twelver313\SheetORM\Helpers;

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Exception;

class Calculations
{
  protected static $cache = [
    'nextColumn' => []
  ];

  /**
   * @throws Exception
   */
  public static function getShiftedColumn(string $letter, int $offset): string
  {
    if (empty($offset)) {
      return $letter;
    }

    $cacheKey = $letter . '_' . $offset;

    if (!isset(self::$cache['nextColumn'][$cacheKey])) {
      $columnIndex = Coordinate::columnIndexFromString($letter);
      $columnIndex += $offset;

      self::$cache['nextColumn'][$cacheKey] = Coordinate::stringFromColumnIndex($columnIndex);
    }

    return self::$cache['nextColumn'][$cacheKey];
  }

  public static function getNextOffset(int $offset, int $step, int $iteration): int
  {
    return $offset + $iteration * $step;
  }
}