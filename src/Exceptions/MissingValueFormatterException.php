<?php

use Twelver313\Sheetmap\Exceptions\SheetmapException;

final class MissingValueFormatterException extends SheetmapException
{
  protected $modelName;
  protected $property;
  protected $type;

  public function __construct(string $type, string $property, string $modelName)
  {
    parent::__construct(
      sprintf(
        "Type '%s' used for property '%s' of class '%s' has no registered formatter",
        $type,
        $property,
        $modelName
      )
    );
    $this->modelName = $modelName;
    $this->property = $property;
    $this->type = $type;
  }

  public function getModelName()
  {
    return $this->modelName;
  }

  public function getProperty()
  {
    return $this->property;
  }

  public function getType()
  {
    return $this->type;
  }
}
