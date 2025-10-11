<?php

namespace Twelver313\SheetORM\Validation;

class ValidateByHeaderSize extends SheetValidationStrategy
{
  const DEFAULT_VALIDATION_MESSAGE_EXACT = "Spreadsheet header row #%u was expected to have exactly %u columns but the provided sheet has %u";
  const DEFAULT_VALIDATION_MESSAGE_MIN = "Spreadsheett header row #%u was expected to have at least %u columns but the provided sheet has %u";
  const DEFAULT_VALIDATION_MESSAGE_MAX = "Spreadsheett header row #%u was expected to have a maximum of %u columns but the provided sheet has %u";
  const DEFAULT_VALIDATION_MESSAGE_RANGE = "Spreadsheett header row #%u was expected to have a minimum of %u and a maximum of %u columns but the provided sheet has %u";

  protected function validate(array $params, SheetValidationContext $context): bool
  {
    $headerSize = $context->getHeaderSize($params['scope'] ?? null);

    /** Validating ranges only if exact param wasn't provided */
    if (!isset($params['exact'])) {
      $validMin = !isset($params['min']) || $headerSize >= intval($params['min']);
      $validMax = !isset($params['max']) || $headerSize <= intval($params['max']);
      return $validMin && $validMax;
    }

    return intval($params['exact']) === $headerSize;
  }

  protected function message(array $params, SheetValidationContext $context): string
  {
    $headerSize = $context->getHeaderSize($params['scope'] ?? null);
    $rowNumber = $context->getSheetHeader()->getScopeRowNumber($params['scope'] ?? null);
    if (isset($params['exact'])) {
      $message = self::DEFAULT_VALIDATION_MESSAGE_EXACT;
      $sprintfParams = [$rowNumber, $params['exact'], $headerSize];
    } else if (isset($params['min']) && isset($params['max'])) {
      $message = self::DEFAULT_VALIDATION_MESSAGE_RANGE;
      $sprintfParams = [$rowNumber, $params['min'], $params['max'], $headerSize];
    } else if (isset($params['min'])) {
      $message = self::DEFAULT_VALIDATION_MESSAGE_MIN;
      $sprintfParams = [$rowNumber, $params['min'], $headerSize];
    } else if (isset($params['max'])) {
      $message = self::DEFAULT_VALIDATION_MESSAGE_MAX;
      $sprintfParams = [$rowNumber, $params['max'], $headerSize];
    } else {
      return '';
    }

    return sprintf($message, ...$sprintfParams);
  }
}
