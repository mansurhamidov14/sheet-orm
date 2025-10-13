<?php

require_once(__DIR__ . '/../vendor/autoload.php');

use Twelver313\SheetORM\ArraySchema;
use Twelver313\SheetORM\Mapping\ArrayMapping;
use Twelver313\SheetORM\Validation\ValidateByHeaderSize;
use Twelver313\SheetORM\Formatter;

$userSchema = new ArraySchema('userSchema');
$userSchema->pickOutHeaderRow();
$userSchema->pickOutHeaderRow(4, 'daySchema');
$userSchema->addSheetValidator(ValidateByHeaderSize::class, ['exact' => 3]);

$userSchema->map(function (ArrayMapping $mapping) {
  $mapping->field('firstName')->title('Name')->type(Formatter::TYPE_STRING);
  $mapping->field('lastName')->title('Last name')->type(Formatter::TYPE_STRING);
  $mapping->field('months')
    ->groupList('monthSchema', ['size' => 3, 'step' => 4])
    ->map(function (ArrayMapping $mapping) {
      $mapping->field('days')
        ->groupList('daySchema', ['size' => 2, 'step' => 2])
        ->map(function (ArrayMapping $mapping) {
          $mapping->field('date')->title('Date')->type(Formatter::TYPE_DATE);
          $mapping->field('time')->title('Time')->type(Formatter::TYPE_TIME);
        });
    });
});

// var_dump($userSchema->getMapping());die;
$mapper = new \Twelver313\SheetORM\SpreadsheetMapper();
$data = $mapper->loadAsArray($userSchema)->fromFile(__DIR__ . '/columngrouplist.xlsx')->getData();
$res = json_encode($data, JSON_PRETTY_PRINT);
file_put_contents(__DIR__ . '/../outputs/output_' . date('U') . '.json', $res);