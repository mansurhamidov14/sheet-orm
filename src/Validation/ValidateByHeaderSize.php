<?php

namespace Twelver313\Sheetmap\Validation;

class ValidateByHeaderSize extends ValidationStrategy
{
  const DEFAULT_VALIDATION_MESSAGE_EXACT = "Spreadsheet was expected to have exactly %u columns but the provided sheet has %u";
  const DEFAULT_VALIDATION_MESSAGE_MIN = "Spreadsheet was expected to have at least %u columns but the provided sheet has %u";
  const DEFAULT_VALIDATION_MESSAGE_MAX = "Spreadsheet was expected to have a maximum of %u columns but the provided sheet has %u";
  const DEFAULT_VALIDATION_MESSAGE_RANGE = "Spreadsheet was expected to have a minimum of %u and a maximum of %u columns but the provided sheet has %u";

  protected function validate(array $params, ValidationContext $context): bool
  {
    $headerSize = $context->getHeaderSize();

    /** Validating ranges only if exact param wasn't provided */
    if (!isset($params['exact'])) {
      $validMin = !isset($params['min']) || $headerSize >= intval($params['min']);
      $validMax = !isset($params['max']) || $headerSize <= intval($params['max']);
      return $validMin && $validMax;
    }

    return intval($params['exact']) === $headerSize;
  }

  protected function message(array $params, ValidationContext $context): string
  {
    $headerSize = $context->getHeaderSize();
    if (isset($params['exact'])) {
      $message = self::DEFAULT_VALIDATION_MESSAGE_EXACT;
      $sprintfParams = [$params['exact'], $headerSize];
    } else if (isset($params['min']) && isset($params['max'])) {
      $message = self::DEFAULT_VALIDATION_MESSAGE_RANGE;
      $sprintfParams = [$params['min'], $params['max'], $headerSize];
    } else if (isset($params['min'])) {
      $message = self::DEFAULT_VALIDATION_MESSAGE_MIN;
      $sprintfParams = [$params['min'], $headerSize];
    } else if (isset($params['max'])) {
      $message = self::DEFAULT_VALIDATION_MESSAGE_MAX;
      $sprintfParams = [$params['max'], $headerSize];
    } else {
      return '';
    }

    return sprintf($message, ...$sprintfParams);
  }
}