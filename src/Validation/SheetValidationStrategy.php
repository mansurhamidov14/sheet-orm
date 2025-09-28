<?php

namespace Twelver313\Sheetmap\Validation;

use Twelver313\Sheetmap\Exceptions\InvalidSheetTemplateException;

abstract class SheetValidationStrategy
{
  abstract protected function validate(array $params, SheetValidationContext $context): bool;

  protected function message(array $params, SheetValidationContext $context): string
  {
    return "Provided sheet doesn't match correct template";
  }

  protected function formatProvidedMessage(string $message, array $params, SheetValidationContext $context)
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

  public function handleValidation(array $params, SheetValidationContext $context, string|null $message = null): void
  {
    $isValid = $this->validate($params, $context, $message);

    if ($isValid) return;

    $errorMessage = isset($message)
      ? $this->formatProvidedMessage($message, $params, $context)
      : $this->message($params, $context);

    throw new InvalidSheetTemplateException($errorMessage, $params, $context);
  }
}
