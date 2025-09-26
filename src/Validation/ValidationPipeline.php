<?php

namespace Twelver313\Sheetmap\Validation;

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
   * @throws \Twelver313\Sheetmap\Exceptions\InvalidSheetTemplateException
   */
  public function validateAll(ValidationContext $context) {
    /** @var Validation */
    foreach ($this->validators as $validator) {
      $validator->getStrategyInstance()->handleValidation($validator->params, $context, $validator->message);
    }
  }

  public function addValidator(Validation $validator)
  {
    $this->validators[] = $validator;
  }
}
