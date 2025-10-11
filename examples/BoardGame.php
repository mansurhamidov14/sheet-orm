<?php

require_once(__DIR__ . '/../vendor/autoload.php');

use PhpOffice\PhpSpreadsheet\Cell\Cell;
use Twelver313\SheetORM\SpreadsheetMapper;
use Twelver313\SheetORM\Attributes\Column;
use Twelver313\SheetORM\Attributes\SheetValidation;

/** @SheetValidation(strategy="Twelver313\SheetORM\Validation\ValidateByHeaderSize", params={"exact": 57}) */
class BoardGame {
  /** @Column(title="row_id", type="int") */
  public $id;

  /** @Column(title="boardgame") */
  public $game;

  /** @Column(title="release_year", type="int") */
  public $releaseYear;

  /** @Column(title="min_players", type="int") */
  public $minPlayers;

  /** @Column(title="max_players", type="int") */
  public $maxPlayers;

  /** @Column(title="avg_rating", type="float") */
  public $avgRating;

  /** @Column(title="num_ratings", type="int") */
  public $numRatings;

  /** @Column(title="wishlisted", type="int") */
  public $wishlisted;

  /** @Column(title="total_plays", type="int") */
  public $totalPlays;

  /** @Column(title="amazon_price", type="float") */
  public $amazonPrice;

  /** @Column(title="comments", type="int") */
  public $comments;

  /** @Column(title="monthly_plays", type="int") */
  public $monthlyPlays;

  /** @Column(title="categories", type="list") */
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

