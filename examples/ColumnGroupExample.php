<?php

require_once(__DIR__ . '/../vendor/autoload.php');

use Twelver313\SheetORM\Attributes\ColumnGroupList;
use Twelver313\SheetORM\Attributes\Column;
use Twelver313\SheetORM\Attributes\TitleRow;
use Twelver313\SheetORM\Attributes\TitleRows;
use Twelver313\SheetORM\Attributes\SheetValidation;
use Twelver313\SheetORM\Attributes\SheetValidators;
use Twelver313\SheetORM\Formatter;
use Twelver313\SheetORM\SpreadsheetMapper;
use Twelver313\SheetORM\Validation\ValidateByHeaderSize;

class UserSchedule
{
    /** @Column(title="Date", type="date") */
    public $date;

    /** @Column(title="Time", type="time") */
    public $time;

    public function toArray()
    {
        return [
            'date' => $this->date?->format('Y-m-d'),
            'time' => $this->time?->format('H:i:s')
        ];
    }
}

class UserScheduleMonth
{
    /** @Column(letter="C", type="monthName") */
    public $monthName;

    /** @ColumnGroupList(target="UserSchedule", size=2, step=2) */
    public $schedules;

    public function toArray()
    {
        return [
            'monthName' => $this->monthName,
            'schedules' => array_map(fn ($i) => $i->toArray(), $this->schedules ?? [])
        ];
    }
}

/**
 * @TitleRows({
 *  @TitleRow(row=1),
 *  @TitleRow(scope="MonthNames", row=2),
 *  @TitleRow(scope="UserSchedule", row=4),
 * })
 * @SheetValidators({
 *  @SheetValidation(strategy=ValidateByHeaderSize::class, params={"exact"=3}),
 *  @SheetValidation(strategy=ValidateByHeaderSize::class, params={"exact"=2, "scope"=UserSchedule::class})
 * })
 */
class User
{
    /** @Column(title="Name") */
    protected $firstName;

    /** @Column(title="Last name") */
    protected $lastName;

    /** @ColumnGroupList(target="UserScheduleMonth", size=3, step=4) */
    protected $months;

    public function toArray()
    {
        return [
            'firstName' => $this->firstName,
            'lastName' => $this->lastName,
            'months' => array_map(fn ($i) => $i->toArray(), $this->months ?? [])
        ];
    }
}

$mapper = new SpreadsheetMapper();
$mapper->formatter->register('monthName', function (Formatter $formatter) {
    return $formatter->context->getHeaderTitle('MonthNames');
});
$data = $mapper->load(User::class)->fromFile(__DIR__ . '/columngrouplist.xlsx')->getData();
$output = json_encode(array_map(fn ($i) => $i->toArray(), $data), JSON_PRETTY_PRINT);
// $output = str_replace('    ', ' ', $output);
// $output = str_replace("\n\n", "\n", $output);
file_put_contents(__DIR__ . '/../outputs/output_' . date('Y-m-d-H-i-s') . '.json', $output);