<?php

namespace Twelver313\SheetORM;

use DateTime;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Twelver313\SheetORM\Exceptions\InvalidValueFormatterTypeException;
use Twelver313\SheetORM\Exceptions\MissingValueFormatterException;
use Twelver313\SheetORM\Field\FieldMapping;

class ValueFormatter
{
  const TYPE_AUTO = 'auto';
  const TYPE_STRING = 'string';
  const TYPE_BOOL = 'bool';
  const TYPE_BOOLEAN = 'boolean';
  const TYPE_INT = 'int';
  const TYPE_FLOAT = 'float';
  const TYPE_DATE = 'date';
  const TYPE_DATETIME = 'datetime';
  const TYPE_TIME = 'time';
  const TYPE_PERCENT = 'percent';

  public $formatters = [];
  
  public function __construct()
  {
    $this->register(self::TYPE_INT, $this->getNumericValueFormatter(self::TYPE_INT));
    $this->register(self::TYPE_FLOAT, $this->getNumericValueFormatter(self::TYPE_FLOAT));

    $this->register(self::TYPE_AUTO, function (Cell $cell) {
      return $cell->getCalculatedValue();
    });

    $this->register(self::TYPE_STRING, function (Cell $cell) {
      return $this->formatString($cell);
    });

    $this->register([self::TYPE_BOOL, self::TYPE_BOOLEAN], function (Cell $cell) {
      return $this->formatBool($cell);
    });

    $this->register([self::TYPE_DATE, self::TYPE_DATETIME, self::TYPE_TIME], function (Cell $cell) {
      return $this->formatDateTime($cell);
    });

    $this->register(self::TYPE_PERCENT, function (Cell $cell) {
      return $this->formatPercent($cell);
    });
  }

  public function format(Cell $cell, FieldMapping $fieldMapping)
  {
    $type = $fieldMapping->type ?? self::TYPE_AUTO;
    if (isset($this->formatters[$type])) {
      return $this->formatters[$type]($cell, $this);
    }
    
    throw new MissingValueFormatterException(
      $fieldMapping->type,
      $fieldMapping->field,
      $fieldMapping->entityName
    );
  }

  /**
   * @param string|array $typeOrTypes
   * @param callable $callback
   */
  public function register($typeOrTypes, callable $callback)
  {
    if (is_string($typeOrTypes)) {
      return $this->formatters[$typeOrTypes] = $callback;
    } 
    
    foreach ($typeOrTypes as $type) {
      if (is_string($type)) {
        $this->formatters[$type] = $callback;
      } else {
        throw new InvalidValueFormatterTypeException(gettype($type));
      }
    }
  }

  private function getNumericValueFormatter(string $type)
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

  public function formatString(Cell $cell): ?string
  {
    $value = trim((string)$cell->getCalculatedValue());
    return $value == '' ? null : $value;
  }

  public function formatDateTime(Cell $cell): ?DateTime
  {
    $value = $cell->getValue();
    if (!is_int($value) && !is_float($value) && empty($value)) {
      return null;
    }

    if (Date::isDateTime($cell)) {
      return Date::excelToDateTimeObject($cell->getValue());
    }

    $dateTime = new DateTime();
    $timeStamp = strtotime($value);

    if ($timeStamp === false) return null;

    $dateTime->setTimestamp($timeStamp);
    return $dateTime;
  }

  public function formatBool(Cell $cell): bool
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

  public function formatPercent(Cell $cell): string
  {
    $value = floatval($cell->getCalculatedValue()) * 100;
    return "{$value}%";
  }
}
