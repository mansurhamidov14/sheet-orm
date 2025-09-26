<?php

namespace Twelver313\Sheetmap\Validation;

class ValidateByHeaderTitles extends ValidationStrategy
{
  protected function validate(array $params, ValidationContext $context): bool
  {
    $expectedHeader = $params['titles'];
    $providedHeader = $context->getHeaderTitles();

    return empty(array_diff($expectedHeader, $providedHeader));
  }

  protected function message(array $params, ValidationContext $context): string
  {
    return sprintf(
      "Provided sheet header doesn't match expected template. Expected: %s. Provided: %s",
      implode(', ', $params['titles']),
      implode(', ', $context->getHeaderTitles())
    );
  }
}