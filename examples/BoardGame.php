<?php

require_once(__DIR__ . '/../vendor/autoload.php');

use PhpOffice\PhpSpreadsheet\Cell\Cell;
use Twelver313\Sheetmap\SpreadsheetMapper;
use Twelver313\Sheetmap\Attributes\SheetColumn;
use Twelver313\Sheetmap\Attributes\SheetValidation;

/** @SheetValidation(strategy="Twelver313\Sheetmap\Validation\ValidateByHeaderSize", params={"exact": 57}) */
class BoardGame {
  /** @SheetColumn(title="row_id", type="int") */
  public $id;

  /** @SheetColumn(title="boardgame") */
  public $game;

  /** @SheetColumn(title="release_year", type="int") */
  public $releaseYear;

  /** @SheetColumn(title="min_players", type="int") */
  public $minPlayers;

  /** @SheetColumn(title="max_players", type="int") */
  public $maxPlayers;

  /** @SheetColumn(title="avg_rating", type="float") */
  public $avgRating;

  /** @SheetColumn(title="num_ratings", type="int") */
  public $numRatings;

  /** @SheetColumn(title="wishlisted", type="int") */
  public $wishlisted;

  /** @SheetColumn(title="total_plays", type="int") */
  public $totalPlays;

  /** @SheetColumn(title="amazon_price", type="float") */
  public $amazonPrice;

  /** @SheetColumn(title="comments", type="int") */
  public $comments;

  /** @SheetColumn(title="monthly_plays", type="int") */
  public $monthlyPlays;

  /** @SheetColumn(title="categories", type="list") */
  public $categories;
}

$spreadsheetMapper = new SpreadsheetMapper();
$spreadsheetMapper->valueFormatter->register("list", function (Cell $cell) {
  $value = $cell->getValue();
  $list = explode(";", $value);
  return array_map('trim', $list);
});

$data = $spreadsheetMapper
  ->load(BoardGame::class)
  ->fromFile(__DIR__ . '/boardgames.csv')
  ->getData();

print_r($data);die;

