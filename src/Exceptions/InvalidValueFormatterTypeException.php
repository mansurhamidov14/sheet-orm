<?php

namespace Twelver313\Sheetmap\Exceptions;

final class InvalidValueFormatterTypeException extends SheetmapException
{
  protected $providedType;

  public function __construct(string $providedType)
  {
    parent::__construct(
      sprintf(
        "Value formatter type should be type of 'string', '%s' was provided",
        $providedType
      )
    );
    $this->providedType = $providedType;    
  }

  public function getProvidedType(): string
  {
    return $this->providedType;
  }
}