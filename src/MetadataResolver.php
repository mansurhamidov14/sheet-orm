<?php

namespace Twelver313\Sheetmap;

use Twelver313\Sheetmap\SheetConfigInterface;

interface MetadataResolver
{
  public function getEntityName(): string;
  public function getSheetConfig(): SheetConfigInterface;
  public function getSheetValidators(): array;
  public function getEntityType(): string;
}