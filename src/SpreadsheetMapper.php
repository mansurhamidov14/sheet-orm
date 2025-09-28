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

  public function load($modelName): self
  {
    $metadataResolver = $this->metadataRegistry->register($modelName);
    $this->mappingRegistry->registerMissing($metadataResolver);
    $this->currentModel = $modelName;
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
    $metadataResolver = $this->metadataRegistry->register($model);
    $mapping = $this->mappingRegistry->register($metadataResolver);
    $callback($mapping);
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
