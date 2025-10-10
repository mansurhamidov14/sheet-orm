<?php

namespace Twelver313\Sheetmap;

use Doctrine\Common\Annotations\AnnotationReader;
use ReflectionClass;
use Twelver313\Sheetmap\Attributes\ColumnGroupItem;
use Twelver313\Sheetmap\Attributes\ColumnGroupList;
use Twelver313\Sheetmap\Attributes\Sheet;
use Twelver313\Sheetmap\Attributes\SheetColumn;
use Twelver313\Sheetmap\Attributes\SheetHeaderRow;
use Twelver313\Sheetmap\Attributes\SheetValidation;
use Twelver313\Sheetmap\Helpers\Attributes;

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

  public function getColumnAttributes($property): ?SheetColumn
  {
    return $this->getPropertyAttributesByType($property, SheetColumn::class);
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

  /** 
   * @return SheetValidation[]
   */
  public function getSheetValidators(): array
  {
    if (Attributes::attributesSupported()) {
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

  /** 
   * @return SheetHeaderRow[]
   */
  public function getHeaderRows(): array
  {
    if (Attributes::attributesSupported()) {
      $headerRowAttributes =  $this->refClass->getAttributes(SheetHeaderRow::class);
      if (count($headerRowAttributes)) {
        return array_map(function ($attribute) {
          return $attribute->newInstance();
        }, $headerRowAttributes);
      }
    }
    
    return array_filter($this->classAnnotations, function ($annotation) {
      return $annotation instanceof SheetHeaderRow;
    });
  }

  public function getEntityType(): string
  {
    return $this->getEntityName();
  }
}
