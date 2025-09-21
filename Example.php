<?php

require_once(__DIR__ . '/vendor/autoload.php');

use Twelver313\Sheetmap\Column;
use Twelver313\Sheetmap\SpreadsheetMapper;

class Example
{
    #[Column(type: "string", letter: "A")]
    public $name;

    #[Column(type: "date", letter: "B")]
    public $birthdate;
}

$mapper = new SpreadsheetMapper;
$res = $mapper->load(Example::class)->fromFile(__DIR__ . '/example.xlsx');
var_dump($res);die;