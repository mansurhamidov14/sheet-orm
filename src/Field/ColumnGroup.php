<?php

namespace Twelver313\Sheetmap\Field;

use Twelver313\Sheetmap\Mapping\MappingProvider;

final class ColumnGroup
{
  /** @var MappingProvider */
  private $mappingProvider;
  private $params;
  private $isList;

  public function __construct(MappingProvider $mappingProvider, array $params = [], bool $isList = false)
  {
    $this->mappingProvider = $mappingProvider;
    $this->params = $params;
    $this->isList = $isList;
  }

  public function isList(): bool
  {
    return $this->isList;
  }

  public function getParams(): array
  {
    return $this->params;
  }

  public function setParams(array $params): void
  {
    $this->params = $params;
  }

  public function getMappingProvider(): MappingProvider
  {
    return $this->mappingProvider;
  }
}