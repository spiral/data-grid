<?php

declare(strict_types=1);

namespace Spiral\DataGrid\Specification\Value;

use Spiral\DataGrid\Specification\ValueInterface;

/**
 * Validates scalar values (string, int, float, bool) with optional empty string handling.
 * More permissive than StringValue - accepts any scalar type and converts to appropriate format.
 *
 * Scalar types in PHP:
 * - string: 'hello', '123', ''
 * - int: 123, 0, -456
 * - float: 12.34, 0.0, -5.67
 * - bool: true, false
 *
 * ```
 * // Mixed form input processing
 * $formValue = new ScalarValue();
 * $formFilter = new Equals('user_input', $formValue);
 * $result = $formFilter->withValue('text input');  // Valid - string
 * $result = $formFilter->withValue(123);           // Valid - integer
 * $result = $formFilter->withValue(45.67);         // Valid - float
 * $result = $formFilter->withValue(true);          // Valid - boolean
 * ```
 * ```
 * // API parameter that accepts various types
 * $apiValue = new ScalarValue();
 * $apiFilter = new Equals('parameter', $apiValue);
 * $result = $apiFilter->withValue('string_param');  // String parameter
 * $result = $apiFilter->withValue(100);             // Numeric parameter
 * $result = $apiFilter->withValue(true);            // Boolean parameter
 * ```
 * ```
 * // Configuration setting (can be text, number, or boolean)
 * $configValue = new ScalarValue();
 * $settingFilter = new Equals('setting_value', $configValue);
 * $result = $settingFilter->withValue('debug');     // String setting
 * $result = $settingFilter->withValue(5);           // Numeric setting
 * $result = $settingFilter->withValue(false);       // Boolean setting
 * ```
 * ```
 * // Search input (accepts text or numbers)
 * $searchValue = new ScalarValue();
 * $searchFilter = new Like('search_term', $searchValue);
 * $result = $searchFilter->withValue('product name'); // Text search
 * $result = $searchFilter->withValue(12345);          // SKU/ID search
 * ```
 * ```
 * // Allow empty strings option
 * $allowEmptyValue = new ScalarValue(true);  // Allow empty strings
 * $strictValue = new ScalarValue(false);     // Reject empty strings (default)
 *
 * $allowEmptyValue->accepts('');     // true - empty allowed
 * $allowEmptyValue->accepts('text'); // true - non-empty string
 * $strictValue->accepts('');         // false - empty not allowed
 * $strictValue->accepts('text');     // true - non-empty string
 * ```
 * ```
 * // Data import processing (CSV with mixed types)
 * $csvValue = new ScalarValue(true); // Allow empty fields
 * $importFilter = new Equals('csv_field', $csvValue);
 * $result = $importFilter->withValue('text data');  // String field
 * $result = $importFilter->withValue(123.45);       // Numeric field
 * $result = $importFilter->withValue('');           // Empty field (allowed)
 * ```
 * ```
 * // User preference storage
 * $preferenceValue = new ScalarValue();
 * $userFilter = new Map([
 *     'theme' => new Equals('theme', $preferenceValue),        // 'dark', 'light'
 *     'font_size' => new Equals('font_size', $preferenceValue), // 12, 14, 16
 *     'notifications' => new Equals('notifications', $preferenceValue) // true, false
 * ]);
 * ```
 * ```
 * // Validation examples
 * $scalarValue = new ScalarValue(); // Default: empty strings not allowed
 *
 * // Valid inputs (scalar types)
 * $scalarValue->accepts('hello');    // true - string
 * $scalarValue->accepts('123');      // true - string
 * $scalarValue->accepts(123);        // true - integer
 * $scalarValue->accepts(12.3);       // true - float
 * $scalarValue->accepts(true);       // true - boolean
 * $scalarValue->accepts(false);      // true - boolean
 * $scalarValue->accepts(0);          // true - integer zero
 * $scalarValue->accepts(0.0);        // true - float zero
 *
 * // Invalid inputs (non-scalar or empty string by default)
 * $scalarValue->accepts('');         // false - empty string (default behavior)
 * $scalarValue->accepts([]);         // false - array
 * $scalarValue->accepts(null);       // false - null
 * $scalarValue->accepts(new \stdClass()); // false - object
 * ```
 *
 * Important notes:
 * - Accepts any scalar type: string, int, float, bool
 * - Does not convert types - returns values as-is
 * - Empty string handling controlled by constructor parameter
 * - More permissive than StringValue, IntValue, etc.
 * - Useful for dynamic content with unknown types
 * - Arrays, objects, and null are always rejected
 * - Zero values (0, 0.0, false) are considered valid scalars
 * - Good for flexible APIs and mixed data processing
 */
final class ScalarValue implements ValueInterface
{
    public function __construct(
        private readonly bool $allowEmpty = false,
    ) {}

    public function accepts(mixed $value): bool
    {
        return \is_scalar($value) && ($this->allowEmpty || $value !== '');
    }

    public function convert(mixed $value): mixed
    {
        return $value;
    }
}
