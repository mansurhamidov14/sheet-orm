<?php

namespace Twelver313\Sheetmap;

use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Twelver313\Sheetmap\Exceptions\DocumentWasNotLoadedException;
use Twelver313\Sheetmap\Mapping\MappingRegistry;
use Twelver313\Sheetmap\Mapping\ModelMapping;

class SpreadsheetMapper
{
  /** @var MappingRegistry */
  private $mappingRegistry;

  /** @var MetadataRegistry */
  private $metadataRegistry;

  /** @var Worksheet */
  private $sheet;

  /** @var ValueFormatter */
  public $valueFormatter;

  private $currentModel;

  public function __construct()
  {
    $this->metadataRegistry = new MetadataRegistry();
    $this->mappingRegistry = new MappingRegistry();
    $this->valueFormatter = new ValueFormatter();
  }

  public function load(string $modelName): self
  {
    if (!$this->metadataRegistry->exists($modelName)) {
      $metadataResolver = new ModelMetadata($modelName);
      $this->metadataRegistry->register($metadataResolver);
    }
    $this->mappingRegistry->registerMissing($modelName, new ModelMapping($metadataResolver));
    $this->currentModel = $modelName;
    
    return $this;
  }

  public function loadAsArray(ArraySchema $schema): self
  {
    $this->mappingRegistry->register(
      $schema->getEntityName(),
      $schema->getMapping()
    );
    $this->metadataRegistry->register($schema);
    $this->currentModel = $schema->getEntityName();
    return $this;
  }

  public function fromFile(string $filePath, ?SheetConfig $config = null): SheetHandler
  {
    $metadataResolver = $this->metadataRegistry->get($this->currentModel);
    $sheetHandler = new SheetHandler(
      $filePath,
      $metadataResolver,
      $this->valueFormatter,
      $this->mappingRegistry->get($this->currentModel),
      $config
    );
    return $sheetHandler;
  }

  public function map($model, callable $callback): self
  {
    $metadataResolver = new ModelMetadata($model);
    $this->metadataRegistry->register($metadataResolver);
    $modelMapping = new ModelMapping($metadataResolver);
    $this->mappingRegistry->register($model, $modelMapping);
    $callback($modelMapping);
    return $this;
  }

  public function getSheet(): Worksheet
  {
    if (!isset($this->sheet)) {
      throw new DocumentWasNotLoadedException();
    }

    return $this->sheet;
  }
}
