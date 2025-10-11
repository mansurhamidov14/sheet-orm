<?php

require_once(__DIR__ . '/../vendor/autoload.php');

use PhpOffice\PhpSpreadsheet\Cell\Cell;
use Twelver313\SheetORM\Attributes\Sheet;
use Twelver313\SheetORM\Attributes\Column;
use Twelver313\SheetORM\SpreadsheetMapper;
use Twelver313\SheetORM\ValueFormatter;

#[Sheet(endRow: 5)]
class User {
  #[Column(title: 'Id', type: ValueFormatter::TYPE_INT)]
  public $id;

  #[Column(title: 'First Name', type: ValueFormatter::TYPE_STRING)]
  public $firstName;

  #[Column(title: 'Last Name', type: ValueFormatter::TYPE_STRING)]
  public $lastName;

  #[Column(title: 'Gender', type: 'gender')]
  public $gender;

  #[Column(title: 'Age', type: ValueFormatter::TYPE_INT)]
  public $age;

  #[Column(title: 'Date', type: 'formattedDate')]
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