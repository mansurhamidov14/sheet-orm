<?php

namespace Twelver313\Sheetmap;

use \ReflectionClass;
use \ReflectionProperty;

class ReflectionUtils
{
  public static function findPropertyByName(ReflectionClass $refClass, string $property): ?ReflectionProperty
  {
    return $refClass->hasProperty($property)
      ? $refClass->getProperty($property)
      : null;
  }

  public static function findAttributeInstance(ReflectionProperty $refProperty, string $attributeClass): ?object
  {
    $attributes = $refProperty->getAttributes($attributeClass);
    if (empty($attributes)) {
      return null;
    }

    return $attributes[0]->newInstance();
  }

  public static function findAttributeInstanceOnProperty(ReflectionClass $refClass, string $property, string $attributeClass): ?object
  {
    $refProperty = self::findPropertyByName($refClass, $property);

    if (empty($refProperty)) {
      return null;
    }

    return self::findAttributeInstance($refProperty, $attributeClass);
  }
}