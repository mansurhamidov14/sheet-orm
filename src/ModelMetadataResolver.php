<?php

namespace Twelver313\Sheetmap;

use Doctrine\Common\Annotations\AnnotationReader;
use ReflectionClass;
use ReflectionProperty;
use Twelver313\Sheetmap\Attributes\AttributeHelpers;
use Twelver313\Sheetmap\Attributes\Sheet;
use Twelver313\Sheetmap\Attributes\SheetColumn;
use Twelver313\Sheetmap\Attributes\SheetValidation;
use Twelver313\Sheetmap\MetadataResolver;
use Twelver313\Sheetmap\SheetConfigInterface;

class ModelMetadataResolver implements MetadataResolver
{
  /** @var AnnotationReader */
  private $annotationReader;

  /** @var ReflectionClass */
  private $refClass;
  
  /** @var object[] */
  private $classAnnotations = [];

  public function __construct(string $model)
  {
    $this->annotationReader = new AnnotationReader();
    $this->refClass = new ReflectionClass($model);
    $this->classAnnotations = $this->annotationReader->getClassAnnotations($this->refClass);
  }

  public function getEntityName(): string
  {
    return $this->refClass->getName();
  }

  public function getSheetConfig(): SheetConfigInterface
  {
    if (AttributeHelpers::attributesSupported()) {
      $sheetConfigAttribute = $this->refClass->getAttributes(Sheet::class)[0] ?? null;
      if (isset($sheetConfigAttribute)) {
        return $sheetConfigAttribute->newInstance();
      }
    }

    $configAnnotators = array_filter($this->classAnnotations, function ($annotation) {
      return $annotation instanceof Sheet;
    });

    return $configAnnotators[0] ?? new Sheet();
  }

  public function getColumnAttributes($property): ?SheetColumn
  {
    if (!$this->refClass->hasProperty($property)) {
      return null;
    }

    $refProperty = $this->refClass->getProperty($property);

    if (AttributeHelpers::attributesSupported()) {
      $columnAttributes = $refProperty->getAttributes(SheetColumn::class);
      if (!empty($columnAttributes)) {
        return $columnAttributes[0]->newInstance();
      }
    }

    return $this->annotationReader->getPropertyAnnotation($refProperty, SheetColumn::class);
  }

  public function getModelProperties()
  {
    return $this->refClass->getProperties();
  }

  /** 
   * @return SheetValidation[]
   */
  public function getSheetValidators(): array
  {
    if (AttributeHelpers::attributesSupported()) {
      $validationAttributes =  $this->refClass->getAttributes(SheetValidation::class);
      if (count($validationAttributes)) {
        return array_map(function ($attribute) {
          return $attribute->newInstance();
        }, $validationAttributes);
      }
    }
    
    return array_filter($this->classAnnotations, function ($annotation) {
      return $annotation instanceof SheetValidation;
    });
  }

  public function getEntityType(): string
  {
    return $this->getEntityName();
  }
}
