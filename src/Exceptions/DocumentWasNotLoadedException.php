<?php

namespace Twelver313\Sheetmap\Exceptions;

final class DocumentWasNotLoadedException extends SheetmapException {
  public function __construct()
  {
    parent::__construct('Spreadsheet document was not loaded');
  }
}
