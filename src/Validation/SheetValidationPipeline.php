<?php

namespace Twelver313\SheetORM\Validation;

use Twelver313\SheetORM\Attributes\SheetValidation;
use Twelver313\SheetORM\Exceptions\InvalidSheetTemplateException;
use Twelver313\SheetORM\MetadataResolver;

class SheetValidationPipeline
{
  /** @var SheetValidation[] */
  private $validators = [];

  public static function fromMetadata(MetadataResolver $metadataResolver): self
  {
    $pipeline = new self();
    $modelValidators = $metadataResolver->getSheetValidators();
    foreach ($modelValidators as $validator) {
      $pipeline->addValidator($validator);
    }

    return $pipeline;
  }

  /**
   * @return string[]
   * @throws InvalidSheetTemplateException
   */
  public function validateAll(SheetValidationContext $context, $silent = false): array
  {
    $errors = [];
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
