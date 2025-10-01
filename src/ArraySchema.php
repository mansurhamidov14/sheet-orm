<?php

namespace Twelver313\Sheetmap;

use Twelver313\Sheetmap\ArrayMapping;
use Twelver313\Sheetmap\Attributes\SheetValidation;

class ArraySchema
{
  private $name;

  /** @var SheetValidation[] */
  protected $validators = [];

  /** @var ArrayMapping */
  protected $mapping;

  /** @var SheetConfig */
  protected $sheetConfig;

  public function __construct(string $name, $config = [])
  {
    $this->name = $name;
    $this->sheetConfig = new SheetConfig($config);
    $this->mapping = new ArrayMapping();
  }

  public function addSheetValidator(string $strategy, array $params, ?string $message = null)
  {
    $validator = new SheetValidation($strategy, $params, $message);
    $this->validators[] = ($validator);
  }

  public function mapKeys(callable $callback)
  {
    $callback($this->mapping);
  }

  public function getName()
  {
    return $this->name;
  }

  public function getMapping()
  {
    return $this->mapping;
  }

  public function getSheetConfig()
  {
    return $this->sheetConfig;
  }

  public function getSheetValidators()
  {
    return $this->validators;
  }
}