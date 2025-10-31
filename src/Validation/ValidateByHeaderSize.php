<?php

namespace Twelver313\SheetORM\Validation;

class ValidateByHeaderSize extends SheetValidationStrategy
{
  const DEFAULT_VALIDATION_MESSAGE_EXACT = "Spreadsheet header row #{context.headerRow} was expected to have exactly {exact} columns";
  const DEFAULT_VALIDATION_MESSAGE_MIN = "Spreadsheet header row #{context.headerRow} was expected to have at least {min} columns but the provided sheet has %u";
  const DEFAULT_VALIDATION_MESSAGE_MAX = "Spreadsheet header row #{context.headerRow} was expected to have a maximum of %u columns but the provided sheet has %u";

  public function __construct(SheetValidationContext $context, array $params = [], array $message = [])
  {
    parent::__construct($context, $params, array_merge(
      [
        'exact' => self::DEFAULT_VALIDATION_MESSAGE_EXACT,
        'min' => self::DEFAULT_VALIDATION_MESSAGE_MIN,
        'max' => self::DEFAULT_VALIDATION_MESSAGE_MAX
      ],
      $message
    ));
  }

  protected function validate()
  {
    $headerSize = $this->context->getHeaderSize($this->params['scope'] ?? null);

    if (isset($this->params['exact']) && intval($this->params['exact']) !== $headerSize) {
      $this->addValidationError($this->message['exact']);
      return;
    }

    if (isset($params['min']) && $headerSize < intval($params['min'])) {
      $this->addValidationError($this->message['min']);
    }

    if (isset($params['max']) && $headerSize > intval($params['max'])) {
      $this->addValidationError($this->message['max']);
    }
  }
}
