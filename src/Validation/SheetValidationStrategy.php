<?php

namespace Twelver313\SheetORM\Validation;

use Twelver313\SheetORM\Exceptions\InvalidSheetTemplateException;

abstract class SheetValidationStrategy
{
  /** @var SheetValidationContext */
  protected $context;

  /** @var array */
  protected $message;

  /** @var array */
  protected $params;

  protected $validationErrors = [];

  public function __construct(SheetValidationContext $context, array $params = [], array $message = [])
  {
    $this->context = $context;
    $this->params = $params;
    $this->message = $message;
  }

  abstract protected function validate();

  protected function getError(): string
  {
    $message = implode(" ", $this->validationErrors);
    return $this->formatMessage($message, $this->params, $this->context);
  }

  protected function addValidationError(string $message): void
  {
    $this->validationErrors[] = $message;
  }

  protected function formatMessage(string $message): string
  {
    $templateParams['{context.headerSize}'] = $this->context->getHeaderSize();
    $templateParams['{context.model'] = $this->context->getModel();
    $templateParams['{context.headerTitles'] = implode(', ', $this->context->getHeaderTitles());
    $templateParams['{context.headerColumns'] = implode(', ', $this->context->getHeaderColumns());
    $templateParams['{context.headerRow}'] = $this->context->getSheetHeader()->getRowNumber(@$this->params['scope']);
    foreach ($this->params as $key => $param) {
      $templateParams["{{$key}}"] = is_array($param) ? implode(', ', $param) : strval($param); 
    }

    return strtr($message, $templateParams);
  }

  /**
   * @throws InvalidSheetTemplateException
   */
  public function handleValidation(): void
  {
    $this->validationErrors = [];
    $this->validate();

    if (empty($this->validationErrors)) return;

    throw new InvalidSheetTemplateException($this->getError(), $this->params, $this->context);
  }
}
