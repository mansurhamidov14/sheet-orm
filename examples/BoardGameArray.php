<?php

require_once(__DIR__ . '/../vendor/autoload.php');

use Twelver313\SheetORM\ArraySchema;
use Twelver313\SheetORM\Mapping\ArrayMapping;
use Twelver313\SheetORM\SpreadsheetMapper;
use Twelver313\SheetORM\Validation\ValidateByHeaderSize;
use Twelver313\SheetORM\Formatter;

$arraySchema = new ArraySchema('boardGames', [
  'endRow' => 4
]);
$arraySchema->addSheetValidator(ValidateByHeaderSize::class, ['exact' => 57]);
$arraySchema->map(function (ArrayMapping $mapping) {
  $mapping->field('id')->title('row_id')->type(Formatter::TYPE_INT);
  $mapping->field('game')->title('boardgame');
});

$spreadsheetMapper = new SpreadsheetMapper();

$handler = $spreadsheetMapper
  ->loadAsArray($arraySchema)
  ->fromFile(__DIR__ . '/boardgames.csv');

if (!$handler->isValidSheet()) var_dump($handler->getErrors());
else var_dump($handler->getData());

print_r($data);die;

