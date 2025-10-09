<?php

require_once(__DIR__ . '/../vendor/autoload.php');

use Twelver313\Sheetmap\Attributes\ColumnGroupList;
use Twelver313\Sheetmap\Attributes\Sheet;
use Twelver313\Sheetmap\Attributes\SheetColumn;
use Twelver313\Sheetmap\SpreadsheetMapper;
use Twelver313\Sheetmap\ValueFormatter;

class UserSchedule
{
    #[SheetColumn(letter: 'C', type: ValueFormatter::TYPE_DATE)]
    public $date;

    #[SheetColumn(letter: 'D', type: ValueFormatter::TYPE_DATE)]
    public $time;
}

class UserScheduleMonth
{
    #[ColumnGroupList(target: UserSchedule::class, size: 2, step: 2)]
    public $schedules;
}

#[Sheet(startRow: 4, endRow: 4)]
class User
{
    #[SheetColumn(letter: 'A')]
    public $firstName;

    #[SheetColumn(letter: 'B')]
    public $lastName;

    #[ColumnGroupList(target: UserScheduleMonth::class, size: 3, step: 4)]
    public $months;
}

$mapper = new SpreadsheetMapper();
$data = $mapper->load(User::class)->fromFile(__DIR__ . '/columngrouplist.xlsx')->getData();
$output = print_r($data, true);
$output = str_replace('    ', ' ', $output);
$output = str_replace("\n\n", "\n", $output);
file_put_contents(__DIR__ . '/../output_' . date('U') . '.log', $output);