<?php

namespace Twelver313\Sheetmap;

use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Twelver313\Sheetmap\Exceptions\DocumentWasNotLoadedException;
use Twelver313\Sheetmap\MappingRegistry;
use Twelver313\Sheetmap\MetadataRegistry;
use Twelver313\Sheetmap\SheetHandler;
use Twelver313\Sheetmap\ValueFormatter;

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
      $metadataResolver = new ModelMetadataResolver($modelName);
      $this->metadataRegistry->register($metadataResolver);
    }
    $this->mappingRegistry->registerMissing($modelName, new ModelMapping($metadataResolver));
    $this->currentModel = $modelName;
    
    return $this;
  }

  public function loadArraySchema(ArraySchema $schema): self
  {
    $metadataResolver = new ArraySchemaMetadataResolver($schema);
    $this->mappingRegistry->register(
      $schema->getName(),
      $schema->getMapping()
    );
    $this->metadataRegistry->register($metadataResolver);
    $this->currentModel = $schema->getName();
    return $this;
  }

  public function fromFile(string $filePath, ?SheetConfigInterface $config = null): SheetHandler
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
    $metadataResolver = new ModelMetadataResolver($model);
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
