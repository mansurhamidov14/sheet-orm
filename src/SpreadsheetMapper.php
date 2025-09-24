<?php

namespace Twelver313\Sheetmap;

use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
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

  private $currentClass;

  public function __construct()
  {
    $this->metadataRegistry = new MetadataRegistry();
    $this->mappingRegistry = new MappingRegistry();
    $this->valueFormatter = new ValueFormatter();
    $this->spreadsheetEngine = new SpreadsheetEngine();
  }

  public function load($className): self
  {
    $metadataResolver = $this->metadataRegistry->register($className);
    $this->mappingRegistry->registerMissing($metadataResolver);
    $this->currentClass = $className;
    return $this;
  }

  public function fromFile(string $filePath): array
  {
    $this->spreadsheetEngine = new SpreadsheetEngine();
    $metadataResolver = $this->metadataRegistry->get($this->currentClass);
    $this->spreadsheetEngine->loadFile($filePath, $metadataResolver);
    $groupedColumns = $this->mappingRegistry
      ->get($this->currentClass)
      ->fulfillMissingProperties($this->spreadsheetEngine->getSheetHeader())
      ->getGroupedProperties();
    $rowHydrator = new RowHydrator($this->currentClass, $this->valueFormatter, $groupedColumns);

    $result = [];
    foreach ($this->spreadsheetEngine->fetchRows() as $row)
    {
      $result[] = $rowHydrator->hydrate($row);
    }

    return $result;
  }

  public function map($class, callable $callback): self
  {
    $metadataResolver = $this->metadataRegistry->register($class);
    $mapping = $this->mappingRegistry->register($metadataResolver);
    $callback($mapping);
    return $this;
  }

  public function getSheet(): Worksheet
  {
    if (!isset($this->sheet)) {
      throw new \Exception('Document file was not selected');
    }

    return $this->sheet;
  }
}