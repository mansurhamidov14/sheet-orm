<?php
require_once(__DIR__ . '/../vendor/autoload.php');

use PhpOffice\PhpSpreadsheet\Cell\Cell;
use Twelver313\SheetORM\Attributes\Sheet;
use Twelver313\SheetORM\Attributes\Column;
use Twelver313\SheetORM\SpreadsheetMapper;
use Twelver313\SheetORM\ValueFormatter;

#[Sheet(endRow: 5)]
class GlobalClimateEvent {
  #[Column(title: 'event_id')]
  protected $id;

  #[Column(title: 'date', type: 'formattedDate')]
  protected $date;

  #[Column(title: 'country')]
  protected $country;

  #[Column(title: 'event_type')]
  protected $eventType;

  #[Column(title: 'duration_days', type: ValueFormatter::TYPE_INT)]
  protected $durationDays;

  #[Column(title: 'affected_population', type: ValueFormatter::TYPE_INT)]
  protected $affectedPopulation;

  #[Column(title: 'deaths', type: ValueFormatter::TYPE_INT)]
  protected $deaths;

  #[Column(title: 'injuries', type: ValueFormatter::TYPE_INT)]
  protected $injuries;

  #[Column(title: 'economic_impact_million_usd', type: 'millionDollar')]
  protected $cost;

  #[Column(title: 'infrastructure_damage_score', type: ValueFormatter::TYPE_FLOAT)]
  protected $infrastructureDamageScore;

  #[Column(title: 'international_aid_million_usd', type: 'millionDollar')]
  protected $internationalAid;

  #[Column(title: 'latitude', type: ValueFormatter::TYPE_FLOAT)]
  protected $latitude;

  #[Column(title: 'longitude', type: ValueFormatter::TYPE_FLOAT)]
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

