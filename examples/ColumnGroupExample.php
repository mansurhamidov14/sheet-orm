<?php

require_once(__DIR__ . '/../vendor/autoload.php');

use Twelver313\SheetORM\Attributes\ColumnGroupList;
use Twelver313\SheetORM\Attributes\Column;
use Twelver313\SheetORM\Attributes\SheetHeaderRow;
use Twelver313\SheetORM\SpreadsheetMapper;

class UserSchedule
{
    /** @Column(title="Date", type="date") */
    public $date;

    /** @Column(title="Time", type="time") */
    public $time;
}

class UserScheduleMonth
{
    /** @ColumnGroupList(target="UserSchedule", size=2, step=2) */
    public $schedules;
}

/** @SheetHeaderRow() */
/** @SheetHeaderRow(scope="UserSchedule", row=4) */
class User
{
    /** @Column(title="Name") */
    public $firstName;

    /** @Column(title="Last name") */
    public $lastName;

    /** @ColumnGroupList(target="UserScheduleMonth", size=3, step=4) */
    public $months;
}

$mapper = new SpreadsheetMapper();
$data = $mapper->load(User::class)->fromFile(__DIR__ . '/columngrouplist.xlsx')->getData();
$output = print_r($data, true);
$output = str_replace('    ', ' ', $output);
$output = str_replace("\n\n", "\n", $output);
file_put_contents(__DIR__ . '/../output_' . date('U') . '.log', $output);