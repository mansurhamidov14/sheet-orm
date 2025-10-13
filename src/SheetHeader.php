<?php

namespace Twelver313\SheetORM;

class SheetHeader
{
  /** @var array */
  protected $headers = [];

  /** @var array */
  protected $rowNumbers = [];

  /** @var string */
  protected $defaultScope = 'default';

  public function addRow(int $rowNumber, string $scope, array $header, bool $isDefault = false): void
  {
    $this->headers[$scope] = $header;
    $this->rowNumbers[$scope] = $rowNumber;

    if ($isDefault) {
      $this->defaultScope = $scope;
    }
  }

  public function getScope(?string $scope = null, bool $fallbackToDefault = false): array
  {
    if (isset($scope) && $header = @$this->headers[$scope]) {
      return $header;
    }

    if ((!isset($scope) || $fallbackToDefault) && $header = @$this->headers[$this->defaultScope]) {
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
    return array_search($column, $this->getScope($scope));
  }

  public function getAll(): array
  {
    return $this->headers;
  }

  public function getDefault(): array
  {
    return $this->headers[$this->defaultScope] ?? [];
  }
}