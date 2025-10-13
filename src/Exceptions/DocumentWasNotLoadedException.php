<?php

namespace Twelver313\SheetORM\Exceptions;

final class DocumentWasNotLoadedException extends SheetORMException {
  public function __construct()
  {
    parent::__construct('Spreadsheet document was not loaded');
  }
}
