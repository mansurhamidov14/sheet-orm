<?php

namespace Twelver313\Sheetmap;

class SheetHeader
{
  /** @var array */
  protected $headers = [];

  /** @var string */
  protected $defaultScope = 'default';

  public function addRow(string $scope, array $header, bool $isDefault = false): void
  {
    $this->headers[$scope] = $header;

    if ($isDefault) {
      $this->defaultScope = $scope;
    }
  }

  public function getScope(?string $scope = null, bool $fallbackToDefault = false): array
  {
    if (isset($scope) && $header = $this->headers[$scope]) {
      return $header;
    }

    if ((!isset($scope) || $fallbackToDefault) && $header = $this->headers[$this->defaultScope]) {
      return $header;
    }

    return [];
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