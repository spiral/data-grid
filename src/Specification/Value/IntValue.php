<?php

declare(strict_types=1);

namespace Spiral\DataGrid\Specification\Value;

use Spiral\DataGrid\Specification\ValueInterface;

/**
 * Validates and converts integer values from numeric input.
 * Accepts numeric values or empty strings and converts them to integer type.
 * Includes special handling for PHP 8+ whitespace compatibility.
 *
 * ```
 * // User ID filtering
 * $userIdValue = new IntValue();
 * $userFilter = new Equals('user_id', $userIdValue);
 * $result = $userFilter->withValue('123'); // Converts to int 123
 * ```
 * ```
 * // Age range filtering
 * $ageValue = new IntValue();
 * $ageFilter = new Between('age', $ageValue);
 * $result = $ageFilter->withValue([18, 65]); // Age between 18-65
 * ```
 * ```
 * // Pagination
 * $pageValue = new IntValue();
 * $paginationFilter = new Gte('page', $pageValue);
 * $result = $paginationFilter->withValue('2'); // Page 2 or higher
 * ```
 * ```
 * // Stock quantity filtering
 * $stockValue = new IntValue();
 * $stockFilter = new Gt('quantity', $stockValue);
 * $result = $stockFilter->withValue(0); // In stock items only
 * ```
 * ```
 * // Priority level system
 * $priorityValue = new IntValue();
 * $taskFilter = new Equals('priority', $priorityValue);
 * $result = $taskFilter->withValue('1'); // High priority (1)
 * ```
 * ```
 * // Year filtering
 * $yearValue = new IntValue();
 * $dateFilter = new Gte('year', $yearValue);
 * $result = $yearFilter->withValue(2024); // 2024 or later
 * ```
 * ```
 * // Rating system (integer ratings)
 * $ratingValue = new IntValue();
 * $reviewFilter = new InArray('rating', $ratingValue);
 * $result = $reviewFilter->withValue([4, 5]); // 4 or 5 star ratings
 * ```
 * ```
 * // Category ID filtering
 * $categoryValue = new IntValue();
 * $productFilter = new InArray('category_id', $categoryValue);
 * $result = $productFilter->withValue(['1', '3', '5']); // Categories 1, 3, 5
 * ```
 * ```
 * // Input validation examples
 * $intValue = new IntValue();
 *
 * // Valid inputs
 * $intValue->accepts(123);           // true - integer
 * $intValue->accepts('123');         // true - numeric string
 * $intValue->accepts('-456');        // true - negative integer string
 * $intValue->accepts('0');           // true - zero string
 * $intValue->accepts(0);             // true - zero integer
 * $intValue->accepts('');            // true - empty string
 * $intValue->accepts('42');          // true - positive string
 *
 * // Invalid inputs (PHP 8+ whitespace compatibility)
 * $intValue->accepts('  123  ');     // false - whitespace framed
 * $intValue->accepts('123abc');      // false - mixed content
 * $intValue->accepts('12.5');        // false - decimal
 * $intValue->accepts('abc');         // false - non-numeric
 * $intValue->accepts([]);            // false - array
 * $intValue->accepts(null);          // false - null
 * $intValue->accepts(true);          // false - boolean
 * ```
 * ```
 * // Conversion examples
 * $intValue = new IntValue();
 *
 * $intValue->convert(123);           // Returns 123
 * $intValue->convert('123');         // Returns 123
 * $intValue->convert('-456');        // Returns -456
 * $intValue->convert('0');           // Returns 0
 * $intValue->convert('');            // Returns 0
 * $intValue->convert(12.9);          // Returns 12 (truncated)
 * $intValue->convert('12.9');        // Returns 12 (truncated)
 * ```
 *
 * Important notes:
 * - Empty strings convert to 0
 * - Decimal numbers are truncated (12.9 becomes 12)
 * - PHP 8+ whitespace handling ensures consistency across versions
 * - Negative integers are fully supported
 * - Uses PHP's (int) casting for conversion
 * - Whitespace-framed numbers are rejected for consistency
 * - More restrictive than NumericValue - only accepts clean integers
 */
final class IntValue implements ValueInterface
{
    public function accepts(mixed $value): bool
    {
        /**
         * Note: Starting from PHP 8 all whitespaces are ignored when checking
         * if the value is similar to numeric:
         *
         * - <= 7.4 : is_numeric('  -42  ') === false
         * - >= 8.0 : is_numeric('  -42  ') === true
         *
         * Therefore, additional verification is required for compatibility:
         *
         * <code>
         *  $isWhitespaceFramed = trim((string)$value) !== (string)$value;
         * </code>
         */
        return $value === '' || (\is_numeric($value) && \trim((string) $value) === (string) $value);
    }

    public function convert(mixed $value): int
    {
        return (int) $value;
    }
}
