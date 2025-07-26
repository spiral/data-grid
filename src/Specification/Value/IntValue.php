<?php

declare(strict_types=1);

namespace Spiral\DataGrid\Specification\Value;

use Spiral\DataGrid\Specification\ValueInterface;

/**
 * Validates and converts integer values from numeric input.
 * Accepts numeric values or empty strings and converts them to integer type.
 * Includes special handling for PHP 8+ whitespace compatibility.
 *
 * Real-world usage examples:
 * - ID filtering: User IDs, product IDs, order numbers
 * - Counting and quantities: Stock levels, page numbers, item counts
 * - Numeric categories: Priority levels, status codes, rating scales
 * - Pagination: Page numbers, items per page, offset values
 * - Age and year values: Birth years, age ranges, experience years
 * - Scoring systems: Integer-based scores, rankings, levels
 * - Configuration values: Port numbers, timeout values, limits
 * - Database keys: Primary keys, foreign keys, reference IDs
 *
 * PHP 8+ compatibility note:
 * - Handles whitespace-framed numbers correctly across PHP versions
 * - Ensures consistent behavior between PHP 7.4 and PHP 8+
 *
 * @example
 * // User ID filtering
 * $userIdValue = new IntValue();
 * $userFilter = new Equals('user_id', $userIdValue);
 * $result = $userFilter->withValue('123'); // Converts to int 123
 *
 * @example
 * // Age range filtering
 * $ageValue = new IntValue();
 * $ageFilter = new Between('age', $ageValue);
 * $result = $ageFilter->withValue([18, 65]); // Age between 18-65
 *
 * @example
 * // Pagination
 * $pageValue = new IntValue();
 * $paginationFilter = new Gte('page', $pageValue);
 * $result = $paginationFilter->withValue('2'); // Page 2 or higher
 *
 * @example
 * // Stock quantity filtering
 * $stockValue = new IntValue();
 * $stockFilter = new Gt('quantity', $stockValue);
 * $result = $stockFilter->withValue(0); // In stock items only
 *
 * @example
 * // Priority level system
 * $priorityValue = new IntValue();
 * $taskFilter = new Equals('priority', $priorityValue);
 * $result = $taskFilter->withValue('1'); // High priority (1)
 *
 * @example
 * // Year filtering
 * $yearValue = new IntValue();
 * $dateFilter = new Gte('year', $yearValue);
 * $result = $yearFilter->withValue(2024); // 2024 or later
 *
 * @example
 * // Rating system (integer ratings)
 * $ratingValue = new IntValue();
 * $reviewFilter = new InArray('rating', $ratingValue);
 * $result = $reviewFilter->withValue([4, 5]); // 4 or 5 star ratings
 *
 * @example
 * // Category ID filtering
 * $categoryValue = new IntValue();
 * $productFilter = new InArray('category_id', $categoryValue);
 * $result = $productFilter->withValue(['1', '3', '5']); // Categories 1, 3, 5
 *
 * @example
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
 *
 * @example
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
 *
 * @example
 * // Order processing
 * $orderIdValue = new IntValue();
 * $orderFilter = new Equals('order_id', $orderIdValue);
 * $result = $orderFilter->withValue($_GET['order_id']); // From URL parameter
 *
 * @example
 * // Batch processing with IDs
 * $idArrayValue = new ArrayValue(new IntValue());
 * $batchFilter = new InArray('id', $idArrayValue);
 * $result = $batchFilter->withValue(['1', '5', '10']); // Process IDs 1, 5, 10
 *
 * @example
 * // Performance monitoring
 * $timeoutValue = new IntValue();
 * $configFilter = new Lt('timeout_seconds', $timeoutValue);
 * $result = $configFilter->withValue(30); // Under 30 seconds
 *
 * @example
 * // Gaming systems
 * $levelValue = new IntValue();
 * $playerFilter = new Gte('player_level', $levelValue);
 * $result = $playerFilter->withValue('10'); // Level 10 or higher
 *
 * @example
 * // Inventory management
 * $stockValue = new IntValue();
 * $lowStockFilter = new Lt('stock_count', $stockValue);
 * $result = $lowStockFilter->withValue(5); // Low stock alert (< 5 items)
 *
 * @example
 * // API parameter validation
 * $limitValue = new IntValue();
 * // GET /api/users?limit=50
 * if ($limitValue->accepts($_GET['limit'])) {
 *     $validLimit = $limitValue->convert($_GET['limit']);
 *     // Use validated integer limit
 * }
 *
 * @example
 * // Form processing
 * $quantityValue = new IntValue();
 * if ($quantityValue->accepts($_POST['quantity'])) {
 *     $orderQuantity = $quantityValue->convert($_POST['quantity']);
 *     // Process valid quantity
 * }
 *
 * @example
 * // Database pagination
 * $offsetValue = new IntValue();
 * $pageSize = 20;
 * $pageNumber = 3;
 * $offsetFilter = new Equals('offset', $offsetValue);
 * $result = $offsetFilter->withValue(($pageNumber - 1) * $pageSize); // Offset 40
 *
 * @example
 * // Status code filtering
 * $statusValue = new IntValue();
 * $errorFilter = new InArray('http_status', $statusValue);
 * $result = $errorFilter->withValue([404, 500, 503]); // Error status codes
 *
 * @example
 * // Time-based filtering (timestamps, hours, etc.)
 * $hourValue = new IntValue();
 * $scheduleFilter = new Between('hour', $hourValue);
 * $result = $scheduleFilter->withValue([9, 17]); // Business hours 9 AM - 5 PM
 *
 * @example
 * // Product rating (integer scale)
 * $ratingValue = new IntValue();
 * $ratingFilter = new Gte('rating', $ratingValue);
 * $result = $ratingFilter->withValue(4); // 4 stars or better
 *
 * @example
 * // Complex integer validation with enum
 * $validPriorityValue = new EnumValue(new IntValue(), 1, 2, 3, 4, 5);
 * $priorityFilter = new Equals('priority_level', $validPriorityValue);
 * $result = $priorityFilter->withValue('2'); // Valid priority level 2
 *
 * @example
 * // Bulk operations
 * $bulkIdValue = new ArrayValue(new IntValue());
 * $bulkDeleteFilter = new InArray('delete_ids', $bulkIdValue);
 * $result = $bulkDeleteFilter->withValue(['10', '25', '37']); // Delete these IDs
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
