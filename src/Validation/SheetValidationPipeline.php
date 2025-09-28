<?php

namespace Twelver313\Sheetmap\Validation;

use Twelver313\Sheetmap\Attributes\SheetValidation;
use Twelver313\Sheetmap\Exceptions\InvalidSheetTemplateException;
use Twelver313\Sheetmap\MetadataResolver;

class SheetValidationPipeline
{
  /** @var SheetValidation[] */
  private $validators = [];

  public static function fromMetadata(MetadataResolver $metadataResolver): self
  {
    $pipeline = new self();
    $modelValidators = $metadataResolver->getModelValidators();
    foreach ($modelValidators as $validator) {
      $pipeline->addValidator($validator);
    }

    return $pipeline;
  }

  /**
   * @return string[]
   * @throws \Twelver313\Sheetmap\Exceptions\InvalidSheetTemplateException
   */
  public function validateAll(SheetValidationContext $context, $silent = false) {
    $errors = [];
    /** @var Validation */
    foreach ($this->validators as $validator) {
      try {
        $validator->getStrategyInstance()->handleValidation($validator->params, $context, $validator->message);
      } catch (InvalidSheetTemplateException $e) {
        $errors[] = $e->getMessage();
        if (!$silent) throw $e;
      }
    }

    return $errors;
  }

  public function addValidator(SheetValidation $validator)
  {
    $this->validators[] = $validator;
  }
}
