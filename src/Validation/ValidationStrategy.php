<?php

namespace Twelver313\Sheetmap\Validation;

use Twelver313\Sheetmap\Exceptions\InvalidSheetTemplateException;

abstract class ValidationStrategy
{
  abstract protected function validate(array $params, ValidationContext $context): bool;

  protected function message(array $params, ValidationContext $context): string
  {
    return "Provided sheet doesn't match correct template";
  }

  protected function formatProvidedMessage(string $message, array $params, ValidationContext $context)
  {
    $templateParams['{context.headerSize}'] = $context->getHeaderSize();
    $templateParams['{context.model'] = $context->getModel();
    $templateParams['{context.headerTitles'] = implode(', ', $context->getHeaderTitles());
    $templateParams['{context.headerColumns'] = implode(', ', $context->getHeaderColumns());
    foreach ($params as $key => $param) {
      $templateParams["{params.{$key}}"] = is_array($param) ? implode(', ', $param) : strval($param); 
    }

    return strtr($message, $templateParams);
  }

  public function handleValidation(array $params, ValidationContext $context, string|null $message = null): void
  {
    $isValid = $this->validate($params, $context, $message);

    if ($isValid) return;

    $errorMessage = isset($message)
      ? $this->formatProvidedMessage($message, $params, $context)
      : $this->message($params, $context);

    throw new InvalidSheetTemplateException($errorMessage, $params, $context);
  }
}
