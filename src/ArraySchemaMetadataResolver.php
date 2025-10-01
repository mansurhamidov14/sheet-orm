<?php

namespace Twelver313\Sheetmap;

use Twelver313\Sheetmap\ArraySchema;
use Twelver313\Sheetmap\MetadataResolver;

class ArraySchemaMetadataResolver implements MetadataResolver
{
  /** @var ArraySchema */
  private $schema;


  private $validators;

  public function __construct(ArraySchema $schema)
  {
    $this->schema = $schema;
  }

  public function getEntityName(): string
  {
    return $this->schema->getName();
  }

  public function getSheetConfig(): SheetConfigInterface
  {
    return $this->schema->getSheetConfig();
  }

  public function getSheetValidators(): array
  {
    return $this->schema->getSheetValidators();
  }

  public function getEntityType(): string
  {
    return ArraySchema::class;
  }
}