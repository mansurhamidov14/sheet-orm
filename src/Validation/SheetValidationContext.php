<?php

namespace Twelver313\SheetORM\Validation;

use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Twelver313\SheetORM\SheetHeader;

class SheetValidationContext
{
  /** @var string */
  private $model;

  /** @var SheetHeader */
  private $sheetHeader;

  /** @var Worksheet */
  private $sheet;

  public function __construct(string $model, SheetHeader $sheetHeader, Worksheet $sheet)
  {
    $this->model = $model;
    $this->sheetHeader = $sheetHeader;
    $this->sheet = $sheet;
  }

  public function getModel(): string
  {
    return $this->model;
  }

  public function getSheet(): Worksheet {
    return $this->sheet;
  }

  public function getSheetHeader(): SheetHeader
  {
    return $this->sheetHeader;
  }

  public function getHeaderColumns(?string $scope = null): array
  {
    return array_values($this->sheetHeader->getScope($scope));
  }

  public function getHeaderTitles(?string $scope = null): array
  {
    return array_keys($this->sheetHeader->getScope($scope));
  }

  public function getHeaderSize(?string $scope = null): int
  {
    return count($this->sheetHeader->getScope($scope));
  }

  public function getHeaderTitle(string $column, ?string $scope = null): ?string
  {
    $title = array_search($column, $this->sheetHeader->getScope($scope));
    return $title === false ? null : $title;
  }

  public function getHeaderColumn(string $title, ?string $scope = null): ?string
  {
    return $this->sheetHeader->getScope($scope)[$title] ?? null;
  }
}
