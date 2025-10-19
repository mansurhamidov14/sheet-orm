<?php

namespace Twelver313\SheetORM;

class SheetHeader
{
  /** @var array */
  protected $rows = [];

  /** @var array */
  protected $rowNumbers = [];

  /** @var string */
  protected $defaultScope = 'default';

  public function addTitleRow(int $rowNumber, string $scope, array $header, bool $isDefault = false): void
  {
    $this->rows[$scope] = $header;
    $this->rowNumbers[$scope] = $rowNumber;

    if ($isDefault) {
      $this->defaultScope = $scope;
    }
  }

  public function getTitleRow(?string $scope = null, bool $fallbackToDefault = false): array
  {
    if (isset($scope) && $header = @$this->rows[$scope]) {
      return $header;
    }

    if ((!isset($scope) || $fallbackToDefault) && $header = @$this->rows[$this->defaultScope]) {
      return $header;
    }

    return [];
  }

  public function getRowNumber(?string $scope): int
  {
    return $this->rowNumbers[$scope ?? $this->defaultScope] ?? 0;
  }

  public function getTitle(string $column, ?string $scope = null)
  {
    return array_search($column, $this->getTitleRow($scope));
  }

  public function getAllRows(): array
  {
    return $this->rows;
  }

  public function getDefault(): array
  {
    return $this->rows[$this->defaultScope] ?? [];
  }
}