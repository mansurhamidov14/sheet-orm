<?php

namespace Twelver313\Sheetmap;

use \DateTime;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class ValueFormatter
{
  const TYPE_STRING = 'string';
  const TYPE_BOOL = 'bool';
  const TYPE_BOOLEAN = 'boolean';
  const TYPE_INT = 'int';
  const TYPE_FLOAT = 'float';
  const TYPE_DATE = 'date';
  const TYPE_DATETIME = 'datetime';
  const TYPE_TIME = 'time';
  const TYPE_PERCENT = 'percent';
  
  public static function formatValue(Cell $cell, mixed $type)
  {
    $value = $cell->getCalculatedValue();
    
    switch ($type) {
      case self::TYPE_STRING:
        return self::formatString($cell);
      case self::TYPE_INT:
      case self::TYPE_FLOAT:
        return self::getNumericValueFormatter($type)($cell);
      case self::TYPE_DATE:
      case self::TYPE_TIME:
      case self::TYPE_DATETIME:
        return self::formatDateTime($cell);
      case self::TYPE_BOOLEAN:
      case self::TYPE_BOOL:
        return self::formatBool($cell);
      case self::TYPE_PERCENT:
        return self::formatPercent($cell);
      default: return $value;
    }
  }

  private static function getNumericValueFormatter(string $type)
  {
    return function (Cell $cell) use ($type) {
      $value = $cell->getCalculatedValue();
      $formatterfunc = "{$type}val";
      if ($value == '#DIV/0!') {
        return null;
      }
      return call_user_func($formatterfunc, $value);
    };
  }

  private static function formatString(Cell $cell)
  {
    $value = trim((string)$cell->getCalculatedValue());
    return $value == '' ? null : $value;
  }

  private static function formatDateTime(Cell $cell)
  {
    $value = $cell->getValue();
    if (!is_int($value) && !is_float($value) && empty($value)) {
      return null;
    }

    if (Date::isDateTime($cell)) {
      return Date::excelToDateTimeObject($cell->getValue());
    }

    $dateTime = new DateTime();
    $dateTime->setTimestamp(strtotime($value));
    return $dateTime;
  }

  private static function formatBool(Cell $cell)
  {
    $value = $cell->getCalculatedValue();
    if (empty($value)) {
      return false;
    }
    
    if (is_numeric($value)) {
      return (float)$value > 0;
    }

    if (is_string($value)) {
      $value = trim(strtolower($value));

      switch ($value) {
        case "":
        case "no":
        case "none":
        case "false":
        case "off":
        case "n":
        case "nope":
          return false;
        default:
          return true;
      }
    }

    return (bool)$value;
  }

  private static function formatPercent(Cell $cell)
  {
    $value = floatval($cell->getCalculatedValue()) * 100;
    return "{$value}%";
  }
}