<?php

namespace Twelver313\Sheetmap;

use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Twelver313\Sheetmap\Exceptions\DocumentWasNotLoadedException;
use Twelver313\Sheetmap\MappingRegistry;
use Twelver313\Sheetmap\MetadataRegistry;
use Twelver313\Sheetmap\RowHydrator;
use Twelver313\Sheetmap\SpreadsheetEngine;
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

  /** @var SpreadsheetEngine */
  private $spreadsheetEngine;

  private $currentModel;

  public function __construct()
  {
    $this->metadataRegistry = new MetadataRegistry();
    $this->mappingRegistry = new MappingRegistry();
    $this->valueFormatter = new ValueFormatter();
    $this->spreadsheetEngine = new SpreadsheetEngine();
  }

  public function load($modelName): self
  {
    $metadataResolver = $this->metadataRegistry->register($modelName);
    $this->mappingRegistry->registerMissing($metadataResolver);
    $this->currentModel = $modelName;
    return $this;
  }

  public function fromFile(string $filePath, SheetConfigInterface|null $config = null): array
  {
    $metadataResolver = $this->metadataRegistry->get($this->currentModel);
    $this->spreadsheetEngine = new SpreadsheetEngine();
    $this->spreadsheetEngine->loadFile($filePath, $metadataResolver, $config);
    $groupedColumns = $this->mappingRegistry
      ->get($this->currentModel)
      ->fulfillMissingProperties($this->spreadsheetEngine->getSheetHeader())
      ->getGroupedProperties();
    $rowHydrator = new RowHydrator($this->currentModel, $this->valueFormatter, $groupedColumns);

    $result = [];
    foreach ($this->spreadsheetEngine->fetchRows() as $row)
    {
      $result[] = $rowHydrator->hydrate($row);
    }

    return $result;
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
