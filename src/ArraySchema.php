<?php

namespace Twelver313\SheetORM;

use Twelver313\SheetORM\Attributes\TitleRow;
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

  /** @var TitleRow[] */
  protected $titleRows;

  public function __construct(string $name, $config = [])
  {
    $this->name = $name;
    $this->sheetConfig = new SheetConfig($config);
    $this->mapping = new ArrayMapping($this);
    $this->titleRows = [];
  }

  public function addSheetValidator(string $strategy, array $params, array $message = []): void
  {
    $validator = new SheetValidation($strategy, $params, $message);
    $this->validators[] = ($validator);
  }

  public function pickOutHeaderRow(int $rowNumber = 1, ?string $scope = null): void
  {
    $this->titleRows[] = new TitleRow($scope ?? $this->getEntityName(), $rowNumber);
  }

  /** @return TitleRow[] */
  public function getTitleRows(): array
  {
    return $this->titleRows;
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