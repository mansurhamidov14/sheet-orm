<?php

namespace Twelver313\Sheetmap\Validation;

use Twelver313\Sheetmap\Exceptions\InvalidSheetTemplateException;
use Twelver313\Sheetmap\MetadataResolver;
use Twelver313\Sheetmap\Validation;

class ValidationPipeline
{
  /** @var Validation[] */
  private $validators = [];

  public static function fromMetadata(MetadataResolver $metadataResolver): self
  {
    $pipeline = new self();
    $validationAttributes = $metadataResolver->getValidationAttributes();
    foreach ($validationAttributes as $attribute) {
      $pipeline->addValidator($attribute->newInstance());
    }

    return $pipeline;
  }

  /**
   * @return string[]
   * @throws \Twelver313\Sheetmap\Exceptions\InvalidSheetTemplateException
   */
  public function validateAll(ValidationContext $context, $silent = false) {
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

  public function addValidator(Validation $validator)
  {
    $this->validators[] = $validator;
  }
}
