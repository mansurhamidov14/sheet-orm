<?php

namespace Twelver313\SheetORM\Exceptions;

final class InvalidValueFormatterTypeException extends SheetORMException
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
