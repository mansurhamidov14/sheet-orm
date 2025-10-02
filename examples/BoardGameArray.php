<?php

require_once(__DIR__ . '/../vendor/autoload.php');

use Twelver313\Sheetmap\ArrayMapping;
use Twelver313\Sheetmap\ArraySchema;
use Twelver313\Sheetmap\SpreadsheetMapper;
use Twelver313\Sheetmap\ValueFormatter;

$arraySchema = new ArraySchema('boardGames', [
  'endRow' => 4
]);

$arraySchema->mapKeys(function (ArrayMapping $mapping) {
  $mapping->field('id')->title('row_id')->type(ValueFormatter::TYPE_INT);
  $mapping->field('game')->title('boardgame');
});

$spreadsheetMapper = new SpreadsheetMapper();

$data = $spreadsheetMapper
  ->loadAsArray($arraySchema)
  ->fromFile(__DIR__ . '/boardgames.csv')
  ->getData();

print_r($data);die;

