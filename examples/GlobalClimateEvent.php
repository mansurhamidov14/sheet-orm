<?php
require_once(__DIR__ . '/../vendor/autoload.php');

use PhpOffice\PhpSpreadsheet\Cell\Cell;
use Twelver313\SheetORM\Attributes\Sheet;
use Twelver313\SheetORM\Attributes\Column;
use Twelver313\SheetORM\SpreadsheetMapper;
use Twelver313\SheetORM\Formatter;

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

  #[Column(title: 'duration_days', type: Formatter::TYPE_INT)]
  protected $durationDays;

  #[Column(title: 'affected_population', type: Formatter::TYPE_INT)]
  protected $affectedPopulation;

  #[Column(title: 'deaths', type: Formatter::TYPE_INT)]
  protected $deaths;

  #[Column(title: 'injuries', type: Formatter::TYPE_INT)]
  protected $injuries;

  #[Column(title: 'economic_impact_million_usd', type: 'millionDollar')]
  protected $cost;

  #[Column(title: 'infrastructure_damage_score', type: Formatter::TYPE_FLOAT)]
  protected $infrastructureDamageScore;

  #[Column(title: 'international_aid_million_usd', type: 'millionDollar')]
  protected $internationalAid;

  #[Column(title: 'latitude', type: Formatter::TYPE_FLOAT)]
  protected $latitude;

  #[Column(title: 'longitude', type: Formatter::TYPE_FLOAT)]
  protected $longitude;
}

$spreadsheetMapper = new SpreadsheetMapper();
$spreadsheetMapper->formatter->register('millionDollar', function (Cell $cell) {
  $val = floatval($cell->getCalculatedValue());
  $val *= 100000;
  return "\${$val}";
});
$spreadsheetMapper->formatter->register('formattedDate', function (Cell $cell) use ($spreadsheetMapper) {
  $dateTime = $spreadsheetMapper->formatter->formatDateTime($cell);

  return $dateTime?->format('d/m/Y');
});

$data = $spreadsheetMapper
  ->load(GlobalClimateEvent::class)
  ->fromFile(__DIR__ . '/globalclimateevents.csv')
  ->getData();

var_dump($data);

