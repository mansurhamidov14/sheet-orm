<?php

require_once(__DIR__ . '/../vendor/autoload.php');

use PhpOffice\PhpSpreadsheet\Cell\Cell;
use Twelver313\Sheetmap\Attributes\Sheet;
use Twelver313\Sheetmap\Attributes\SheetColumn;
use Twelver313\Sheetmap\SpreadsheetMapper;
use Twelver313\Sheetmap\ValueFormatter;

#[Sheet(endRow: 5)]
class User {
  #[SheetColumn(title: 'Id', type: ValueFormatter::TYPE_INT)]
  public $id;

  #[SheetColumn(title: 'First Name', type: ValueFormatter::TYPE_STRING)]
  public $firstName;

  #[SheetColumn(title: 'Last Name', type: ValueFormatter::TYPE_STRING)]
  public $lastName;

  #[SheetColumn(title: 'Gender', type: 'gender')]
  public $gender;

  #[SheetColumn(title: 'Age', type: ValueFormatter::TYPE_INT)]
  public $age;

  #[SheetColumn(title: 'Date', type: 'formattedDate')]
  public $birthDate;
}

$spreadsheetMapper = new SpreadsheetMapper();
$spreadsheetMapper->valueFormatter->register('gender', function (Cell $cell) {
  return match (strtolower($cell->getCalculatedValue())) {
    'male' => '♂',
    'female' => '♀',
    'default' => null
  };
});
$spreadsheetMapper->valueFormatter->register('formattedDate', function (Cell $cell, ValueFormatter $formatter) {
  return $formatter->formatDateTime($cell)?->format('d.m.Y');
});

$handler = $spreadsheetMapper->load(User::class)->fromFile(__DIR__ . '/users.xls');
var_dump($handler->getData());