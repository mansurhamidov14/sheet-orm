<?php

require_once(__DIR__ . '/vendor/autoload.php');

use Twelver313\Sheetmap\Column;
use Twelver313\Sheetmap\SpreadsheetMapper;

class Example
{
    #[Column(type: "string")]
    public $name;

    #[Column(type: "date")]
    public $birthdate;
}

$mapper = new SpreadsheetMapper;
$mapper->init(Example::class);