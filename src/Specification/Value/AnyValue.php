<?php

declare(strict_types=1);

namespace Spiral\DataGrid\Specification\Value;

use Spiral\DataGrid\Specification\ValueInterface;

/**
 * Accepts any input value without validation or conversion.
 * This is the most permissive value type - it passes through any input unchanged.
 *
 * ```
 * // Accept any search term without validation
 * $searchFilter = new Like('content', new AnyValue());
 * $result = $searchFilter->withValue('any string here'); // Passes through unchanged
 * ```
 * ```
 * // Configuration storage
 * $configFilter = new Equals('setting_value', new AnyValue());
 * $result = $configFilter->withValue(['complex' => 'array']); // Stored as-is
 * ```
 * ```
 * // Legacy system compatibility
 * $legacyFilter = new Equals('legacy_field', new AnyValue());
 * $result = $legacyFilter->withValue(12345); // Number
 * $result = $legacyFilter->withValue('text'); // String
 * $result = $legacyFilter->withValue(true); // Boolean - all accepted
 * ```
 * ```
 * // Raw data processing
 * $rawDataFilter = new Equals('raw_input', new AnyValue());
 * $result = $rawDataFilter->withValue($userInput); // Whatever user provided
 * ```
 * ```
 * // Development/testing mode
 * if ($developmentMode) {
 *     $filter = new Like('debug_field', new AnyValue()); // Accept anything for testing
 * } else {
 *     $filter = new Like('debug_field', new StringValue()); // Strict validation in production
 * }
 * ```
 * ```
 * // API endpoint flexibility
 * $apiFilter = new Equals('metadata', new AnyValue());
 * // Clients can send: strings, numbers, arrays, objects, etc.
 * ```
 * ```
 * // Free-form user input
 * $userInputFilter = new Like('user_notes', new AnyValue());
 * // Users can enter anything: text, emojis, special characters, etc.
 * ```
 * ```
 * // Comparison with other value types
 * $anyValue = new AnyValue();
 * $stringValue = new StringValue();
 *
 * $anyValue->accepts(123);        // true
 * $anyValue->accepts('text');     // true
 * $anyValue->accepts([1,2,3]);    // true
 * $anyValue->accepts(true);       // true
 *
 * $stringValue->accepts(123);     // true (converts to string)
 * $stringValue->accepts('text');  // true
 * $stringValue->accepts([1,2,3]); // false (can't convert array to string)
 * $stringValue->accepts(true);    // true (converts to string)
 * ```
 * Note: While AnyValue is very flexible, use it carefully in production systems
 * as it bypasses all validation. Consider using more specific value types when possible
 * for better data integrity and security.
 */
final class AnyValue implements ValueInterface
{
    public function accepts(mixed $value): bool
    {
        return true;
    }

    public function convert(mixed $value): mixed
    {
        return $value;
    }
}
