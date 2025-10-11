<?php

namespace Twelver313\SheetORM;

use Doctrine\Common\Annotations\AnnotationReader;
use ReflectionClass;
use Twelver313\SheetORM\Attributes\ColumnGroupItem;
use Twelver313\SheetORM\Attributes\ColumnGroupList;
use Twelver313\SheetORM\Attributes\Sheet;
use Twelver313\SheetORM\Attributes\Column;
use Twelver313\SheetORM\Attributes\SheetHeaderRow;
use Twelver313\SheetORM\Attributes\SheetHeaderRows;
use Twelver313\SheetORM\Attributes\SheetValidation;
use Twelver313\SheetORM\Attributes\SheetValidators;
use Twelver313\SheetORM\Helpers\Attributes;

class ModelMetadata implements MetadataResolver
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

  public function getSheetConfig(): SheetConfig
  {
    if (Attributes::attributesSupported()) {
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

  public function getPropertyAttributesByType(string $property, string $type)
  {
    if (!$this->refClass->hasProperty($property)) {
      return null;
    }

    $refProperty = $this->refClass->getProperty($property);

    if (Attributes::attributesSupported()) {
      $columnAttributes = $refProperty->getAttributes($type);
      if (!empty($columnAttributes)) {
        return $columnAttributes[0]->newInstance();
      }
    }

    return $this->annotationReader->getPropertyAnnotation($refProperty, $type);
  }

  public function getColumnAttributes($property): ?Column
  {
    return $this->getPropertyAttributesByType($property, Column::class);
  }

  public function getColumnGroupItemAttributes($property): ?ColumnGroupItem
  {
    return $this->getPropertyAttributesByType($property, ColumnGroupItem::class);
  }

  public function getColumnGroupListAttributes($property): ?ColumnGroupList
  {
    return $this->getPropertyAttributesByType($property, ColumnGroupList::class);
  }

  public function getModelProperties()
  {
    return $this->refClass->getProperties();
  }

  public function getRepeatableAttribute(string $attributeClass, string $annotationWrapper): array
  {
    if (Attributes::attributesSupported()) {
      $attributes = $this->refClass->getAttributes($attributeClass);
      if (count($attributes)) {
        return array_map(function ($attribute) {
          return $attribute->newInstance();
        }, $attributes);
      }
    }

    $wrapperAttribute = current(array_filter($this->classAnnotations, function ($annotation) use ($annotationWrapper) {
      return $annotation instanceof $annotationWrapper;
    }));


    if ($wrapperAttribute) {
      return $wrapperAttribute->value;
    }

    return array_filter($this->classAnnotations, function ($annotation) use ($attributeClass) {
      return $annotation instanceof $attributeClass;
    });
  }

  /** 
   * @return SheetValidation[]
   */
  public function getSheetValidators(): array
  {
    return $this->getRepeatableAttribute(SheetValidation::class, SheetValidators::class);
  }

  /** 
   * @return SheetHeaderRow[]
   */
  public function getHeaderRows(): array
  {
    return $this->getRepeatableAttribute(SheetHeaderRow::class, SheetHeaderRows::class);
  }

  public function getEntityType(): string
  {
    return $this->getEntityName();
  }
}
