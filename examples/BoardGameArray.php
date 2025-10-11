<?php

require_once(__DIR__ . '/../vendor/autoload.php');

use Twelver313\SheetORM\ArrayMapping;
use Twelver313\SheetORM\ArraySchema;
use Twelver313\SheetORM\SpreadsheetMapper;
use Twelver313\SheetORM\Validation\ValidateByHeaderSize;
use Twelver313\SheetORM\ValueFormatter;

$arraySchema = new ArraySchema('boardGames', [
  'endRow' => 4
]);
$arraySchema->addSheetValidator(ValidateByHeaderSize::class, ['exact' => 57]);
$arraySchema->mapKeys(function (ArrayMapping $mapping) {
  $mapping->field('id')->title('row_id')->type(ValueFormatter::TYPE_INT);
  $mapping->field('game')->title('boardgame');
});

$spreadsheetMapper = new SpreadsheetMapper();

$handler = $spreadsheetMapper
  ->loadAsArray($arraySchema)
  ->fromFile(__DIR__ . '/boardgames.csv');

if (!$handler->isValidSheet()) var_dump($handler->getErrors());
else var_dump($handler->getData());

print_r($data);die;

