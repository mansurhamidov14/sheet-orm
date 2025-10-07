<?php

namespace Twelver313\Sheetmap;

interface MetadataResolver
{
  public function getEntityName(): string;
  public function getSheetConfig(): SheetConfig;
  public function getSheetValidators(): array;
}
