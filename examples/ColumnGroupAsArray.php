<?php

require_once(__DIR__ . '/../vendor/autoload.php');

use Twelver313\Sheetmap\ArrayMapping;
use Twelver313\Sheetmap\ArraySchema;
use Twelver313\Sheetmap\ValueFormatter;

$daySchema = new ArraySchema('daySchema');
$daySchema->mapKeys(function (ArrayMapping $mapping) {
  $mapping->field('date')->column('C')->type(ValueFormatter::TYPE_DATE);
  $mapping->field('time')->column('D')->type(ValueFormatter::TYPE_TIME);
});

$monthSchema = new ArraySchema('monthSchema');
$monthSchema->mapKeys(function (ArrayMapping $mapping) use ($daySchema) {
  $mapping->field('days')->groupList($daySchema, 2, 2);
});

$userSchema = new ArraySchema('userSchema', ['startRow' => 4, 'endRow' => 4]);
$userSchema->mapKeys(function (ArrayMapping $mapping) use ($monthSchema) {
  $mapping->field('firstName')->column('A')->type(ValueFormatter::TYPE_STRING);
  $mapping->field('lastName')->column('B')->type(ValueFormatter::TYPE_STRING);
  $mapping->field('months')->groupList($monthSchema, 3, 4);
});

// var_dump($userSchema->getMapping());die;
$mapper = new \Twelver313\Sheetmap\SpreadsheetMapper();
$data = $mapper->loadAsArray($userSchema)->fromFile(__DIR__ . '/columngrouplist.xlsx')->getData();
$res = json_encode($data, JSON_PRETTY_PRINT);
file_put_contents(__DIR__ . '/../output_' . date('U') . '.json', $res);