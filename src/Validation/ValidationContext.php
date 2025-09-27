<?php

namespace Twelver313\Sheetmap\Validation;

use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ValidationContext
{
  /** @var string */
  private $model;

  /** @var array */
  private $headerColumns;

  /** @var Worksheet */
  private $sheet;

  public function __construct(string $model, array $headerColumns, Worksheet $sheet)
  {
    $this->model = $model;
    $this->headerColumns = $headerColumns;
    $this->sheet = $sheet;
  }

  public function getModel(): string
  {
    return $this->model;
  }

  public function getSheet(): Worksheet {
    return $this->sheet;
  }

  public function getHeaderColumns(): array
  {
    return array_values($this->headerColumns);
  }

  public function getHeaderTitles(): array
  {
    return array_keys($this->headerColumns);
  }

  public function getHeaderSize(): int
  {
    return count($this->headerColumns);
  }

  public function getHeaderTitle(string $column): string|null
  {
    $title = array_search($column, $this->headerColumns);
    return $title === false ? null : $title;
  }

  public function getHeaderColumn(string $title): string|null
  {
    return $this->headerColumns[$title] ?? null;
  }
}
