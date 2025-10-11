<?php

namespace Twelver313\SheetORM;

interface MetadataResolver
{
  public function getEntityName(): string;
  public function getSheetConfig(): SheetConfig;
  public function getSheetValidators(): array;
  /** @return Attributes\SheetHeaderRow[] */
  public function getHeaderRows(): array;
}
