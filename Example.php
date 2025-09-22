<?php

require_once(__DIR__ . '/vendor/autoload.php');

use Twelver313\Sheetmap\Column;
use Twelver313\Sheetmap\SpreadsheetMapper;
use Twelver313\Sheetmap\ValueFormatter;

class Example
{
    #[Column(type: ValueFormatter::TYPE_STRING, title: "Name")]
    public $name;

    #[Column(type: ValueFormatter::TYPE_DATE, title: "Birth date")]
    public $birthdate;

    #[Column(type: ValueFormatter::TYPE_INT, title: "Age")]
    public $age;

    #[Column(type: ValueFormatter::TYPE_PERCENT, title: "Percent")]
    public $percent;

    #[Column(type: ValueFormatter::TYPE_BOOLEAN, title: "Boolean")]
    public $isTrue;

    #[Column(type: ValueFormatter::TYPE_FLOAT, title: "Formula")]
    public $formula;
}

$mapper = new SpreadsheetMapper;
$res = $mapper->load(Example::class)->fromFile(__DIR__ . '/example2.xlsx');
var_dump($res);die;