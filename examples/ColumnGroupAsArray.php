<?php

require_once(__DIR__ . '/../vendor/autoload.php');

use Twelver313\Sheetmap\ArraySchema;
use Twelver313\Sheetmap\Mapping\ArrayMapping;
use Twelver313\Sheetmap\Validation\ValidateByHeaderSize;
use Twelver313\Sheetmap\ValueFormatter;

$userSchema = new ArraySchema('userSchema', ['endRow' => 5]);
$userSchema->pickOutHeaderRow();
$userSchema->pickOutHeaderRow(4, 'daySchema');
$userSchema->addSheetValidator(ValidateByHeaderSize::class, ['exact' => 2]);

$userSchema->map(function (ArrayMapping $mapping) {
  $mapping->field('firstName')->title('Name')->type(ValueFormatter::TYPE_STRING);
  $mapping->field('lastName')->title('Last name')->type(ValueFormatter::TYPE_STRING);
  $mapping->field('months')
    ->groupList('monthSchema', ['size' => 3, 'step' => 4])
    ->map(function (ArrayMapping $mapping) {
      $mapping->field('days')
        ->groupList('daySchema', ['size' => 2, 'step' => 2])
        ->map(function (ArrayMapping $mapping) {
          $mapping->field('date')->title('Date')->type(ValueFormatter::TYPE_DATE);
          $mapping->field('time')->title('Time')->type(ValueFormatter::TYPE_TIME);
        });
    });
});

// var_dump($userSchema->getMapping());die;
$mapper = new \Twelver313\Sheetmap\SpreadsheetMapper();
$data = $mapper->loadAsArray($userSchema)->fromFile(__DIR__ . '/columngrouplist.xlsx')->getData();
$res = json_encode($data, JSON_PRETTY_PRINT);
file_put_contents(__DIR__ . '/../outputs/output_' . date('U') . '.json', $res);