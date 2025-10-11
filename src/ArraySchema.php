<?php

namespace Twelver313\SheetORM;

use Twelver313\SheetORM\Attributes\SheetHeaderRow;
use Twelver313\SheetORM\Mapping\ArrayMapping;
use Twelver313\SheetORM\Attributes\SheetValidation;

class ArraySchema implements MetadataResolver
{
  private $name;

  /** @var SheetValidation[] */
  protected $validators = [];

  /** @var ArrayMapping */
  protected $mapping;

  /** @var SheetConfig */
  protected $sheetConfig;

  /** @var SheetHeaderRow[] */
  protected $headerRows;

  public function __construct(string $name, $config = [])
  {
    $this->name = $name;
    $this->sheetConfig = new SheetConfig($config);
    $this->mapping = new ArrayMapping($this);
    $this->headerRows = [];
  }

  public function addSheetValidator(string $strategy, array $params, ?string $message = null): void
  {
    $validator = new SheetValidation($strategy, $params, $message);
    $this->validators[] = ($validator);
  }

  public function pickOutHeaderRow(int $rowNumber = 1, ?string $scope = null): void
  {
    $this->headerRows[] = new SheetHeaderRow($scope ?? $this->getEntityName(), $rowNumber);
  }

  /** @return SheetHeaderRow[] */
  public function getHeaderRows(): array
  {
    return $this->headerRows;
  }

  public function map(callable $callback): void
  {
    $this->mapping->map($callback);
  }

  public function getEntityName(): string
  {
    return $this->name;
  }

  public function getMapping(): ArrayMapping
  {
    return $this->mapping;
  }

  public function getSheetConfig(): SheetConfig
  {
    return $this->sheetConfig;
  }

  public function getSheetValidators(): array
  {
    return $this->validators;
  }
}