<?php

namespace Twelver313\Sheetmap\Validation;

class ValidateByHeaderTitles extends SheetValidationStrategy
{
  protected function validate(array $params, SheetValidationContext $context): bool
  {
    $expected = $params['titles'];
    $actual = $context->getHeaderTitles();
    $flags = $params['flags'] ?? 0;

    // 1. Case normalization
    if (HeaderValidationFlags::hasFlag(HeaderValidationFlags::IGNORE_CASE, $flags)) {
      $expected = array_map('mb_strtolower', $expected);
      $actual = array_map('mb_strtolower', $actual);
    }

    // 2. Column count check
    if (HeaderValidationFlags::hasFlag(HeaderValidationFlags::EXACT_COLUMNS, $flags)) {
      if (count($expected) !== count($actual)) {
        return false;
      }
    } else {
      $actual = array_values(array_filter($actual, function ($title) use ($expected) {
        return in_array($title, $expected);
      }));
    }

    // 3. Order-insensitive check
    if (HeaderValidationFlags::hasFlag(HeaderValidationFlags::IGNORE_ORDER, $flags)) {
      // Special case: with regex, need matching algorithm
      if (HeaderValidationFlags::hasFlag(HeaderValidationFlags::REGEXP, $flags)) {
        return self::matchUnorderedRegex($expected, $actual);
      }

      sort($expected);
      sort($actual);
    }

    // 4. Compare element by element
    foreach ($expected as $i => $exp) {
      $act = $actual[$i] ?? null;

      if ($act === null) {
        return false;
      }

      if (HeaderValidationFlags::hasFlag(HeaderValidationFlags::REGEXP, $flags)) {
        if (@preg_match($exp, $act) !== 1) {
          return false;
        }
      } else {
        if ($exp !== $act) {
          return false;
        }
      }
    }

    return true;
  }

  /**
   * Match unordered regex patterns to actual titles.
   */
  private static function matchUnorderedRegex(array $patterns, array $titles): bool
  {
    $matched = [];
    foreach ($patterns as $pattern) {
      $found = false;
      foreach ($titles as $i => $title) {
        if (in_array($i, $matched, true)) {
          continue; // already consumed
        }
        if (@preg_match($pattern, $title)) {
          $matched[] = $i;
          $found = true;
          break;
        }
      }
      if (!$found) {
        return false; // this pattern matched nothing
      }
    }
    return true;
  }

  protected function message(array $params, SheetValidationContext $context): string
  {
    return sprintf(
      "Provided sheet header doesn't match expected template. Expected: %s. Provided: %s",
      implode(', ', $params['titles']),
      implode(', ', $context->getHeaderTitles())
    );
  }
}
