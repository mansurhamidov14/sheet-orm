<?php

namespace Twelver313\Sheetmap;

use Twelver313\Sheetmap\SheetConfig;

interface MetadataResolver
{
  public function getEntityName(): string;
  public function getSheetConfig(): SheetConfig;
  public function getSheetValidators(): array;
}