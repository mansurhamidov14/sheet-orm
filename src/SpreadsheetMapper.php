<?php

namespace Twelver313\SheetORM;

use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Twelver313\SheetORM\Exceptions\DocumentWasNotLoadedException;
use Twelver313\SheetORM\Exceptions\MissingMappingException;
use Twelver313\SheetORM\Exceptions\MissingMetadataException;
use Twelver313\SheetORM\Exceptions\SpreadsheetReaderException;
use Twelver313\SheetORM\Mapping\MappingRegistry;
use Twelver313\SheetORM\Mapping\ModelMapping;

class SpreadsheetMapper
{
  /** @var MappingRegistry */
  private $mappingRegistry;

  /** @var MetadataRegistry */
  private $metadataRegistry;

  /** @var Formatter */
  public $formatter;

  private $currentModel;

  public function __construct()
  {
    $this->metadataRegistry = new MetadataRegistry();
    $this->mappingRegistry = new MappingRegistry();
    $this->formatter = new Formatter();
  }

  public function load(string $modelName): self
  {
    if (!$this->metadataRegistry->exists($modelName)) {
      $metadataResolver = new ModelMetadata($modelName);
      $this->metadataRegistry->register($metadataResolver);
    }
    if (!$this->mappingRegistry->exists($modelName)) {
      $this->mappingRegistry->registerMissing(
        $modelName,
        new ModelMapping(
          $this->metadataRegistry->get($modelName)
        )
      );
    }
    
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

  /**
   * @throws MissingMetadataException
   * @throws SpreadsheetReaderException
   * @throws MissingMappingException
   */
  public function fromFile(string $filePath, ?SheetConfig $config = null): SheetHandler
  {
    $metadataResolver = $this->metadataRegistry->get($this->currentModel);
    return new SheetHandler(
      $filePath,
      $metadataResolver,
      $this->formatter,
      $this->mappingRegistry->get($this->currentModel),
      $config
    );
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
}
