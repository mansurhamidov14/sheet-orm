<?php

namespace Twelver313\Sheetmap\Validation;

class ValidationContext
{
  /** @var string */
  private $entity;

  /** @var array */
  private $headerColumns;

  public function __construct(string $model, array $headerColumns)
  {
    $this->entity = $model;
    $this->headerColumns = $headerColumns;
  }

  public function getEntity()
  {
    return $this->entity;
  }

  public function getHeaderColumns(): array
  {
    return array_values($this->headerColumns);
  }

  public function getHeaderTitles(): array
  {
    return array_keys($this->headerColumns);
  }

  public function getHeaderSize()
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
