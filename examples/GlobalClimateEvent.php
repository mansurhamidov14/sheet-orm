<?php
require_once(__DIR__ . '/../vendor/autoload.php');

use PhpOffice\PhpSpreadsheet\Cell\Cell;
use Twelver313\Sheetmap\Attributes\Sheet;
use Twelver313\Sheetmap\Attributes\SheetColumn;
use Twelver313\Sheetmap\SpreadsheetMapper;
use Twelver313\Sheetmap\ValueFormatter;

#[Sheet(endRow: 5)]
class GlobalClimateEvent {
  #[SheetColumn(title: 'event_id')]
  protected $id;

  #[SheetColumn(title: 'date', type: 'formattedDate')]
  protected $date;

  #[SheetColumn(title: 'country')]
  protected $country;

  #[SheetColumn(title: 'event_type')]
  protected $eventType;

  #[SheetColumn(title: 'duration_days', type: ValueFormatter::TYPE_INT)]
  protected $durationDays;

  #[SheetColumn(title: 'affected_population', type: ValueFormatter::TYPE_INT)]
  protected $affectedPopulation;

  #[SheetColumn(title: 'deaths', type: ValueFormatter::TYPE_INT)]
  protected $deaths;

  #[SheetColumn(title: 'injuries', type: ValueFormatter::TYPE_INT)]
  protected $injuries;

  #[SheetColumn(title: 'economic_impact_million_usd', type: 'millionDollar')]
  protected $cost;

  #[SheetColumn(title: 'infrastructure_damage_score', type: ValueFormatter::TYPE_FLOAT)]
  protected $infrastructureDamageScore;

  #[SheetColumn(title: 'international_aid_million_usd', type: 'millionDollar')]
  protected $internationalAid;

  #[SheetColumn(title: 'latitude', type: ValueFormatter::TYPE_FLOAT)]
  protected $latitude;

  #[SheetColumn(title: 'longitude', type: ValueFormatter::TYPE_FLOAT)]
  protected $longitude;
}

$spreadsheetMapper = new SpreadsheetMapper();
$spreadsheetMapper->valueFormatter->register('millionDollar', function (Cell $cell) {
  $val = floatval($cell->getCalculatedValue());
  $val *= 100000;
  return "\${$val}";
});
$spreadsheetMapper->valueFormatter->register('formattedDate', function (Cell $cell) use ($spreadsheetMapper) {
  $dateTime = $spreadsheetMapper->valueFormatter->formatDateTime($cell);

  return $dateTime?->format('d/m/Y');
});

$data = $spreadsheetMapper
  ->load(GlobalClimateEvent::class)
  ->fromFile(__DIR__ . '/globalclimateevents.csv')
  ->getData();

var_dump($data);

