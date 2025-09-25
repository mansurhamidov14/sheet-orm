<?php

use Twelver313\Sheetmap\Exceptions\SheetmapException;

final class MissingValueFormatterException extends SheetmapException
{
  protected $className;
  protected $property;
  protected $type;

  public function __construct(string $type, string $property, string $className)
  {
    parent::__construct(
      sprintf(
        "Type '%s' used for property '%s' of class '%s' has no registered formatter",
        $type,
        $property,
        $className
      )
    );
    $this->className = $className;
    $this->property = $property;
    $this->type = $type;
  }

  public function getClassName()
  {
    return $this->className;
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