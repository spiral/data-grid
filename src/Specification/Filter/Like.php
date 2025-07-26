<?php

declare(strict_types=1);

namespace Spiral\DataGrid\Specification\Filter;

use Spiral\DataGrid\Specification\Value\StringValue;

/**
 * Filters records where a field value matches a pattern (similar to SQL LIKE).
 *
 * Real-world usage examples:
 * - Search functionality: Find products with names containing "iPhone"
 * - Email filtering: Find users with emails ending in "@company.com"
 * - Content search: Find articles with titles starting with "How to"
 * - File filtering: Find files with names containing specific keywords
 * - Address matching: Find addresses containing street names or postal codes
 * - Log analysis: Find log entries containing error keywords
 * - Autocomplete: Find suggestions that start with user input
 * - Flexible text matching: Custom patterns with wildcards
 *
 * Pattern placeholders:
 * - %s = the search value position
 * - % = wildcard for any characters
 *
 * Common patterns:
 * - '%%%s%%' (default) = contains (anywhere in text)
 * - '%s%%' = starts with
 * - '%%%s' = ends with
 * - Custom patterns for specific matching needs
 *
 * @example
 * // General search (contains pattern - default)
 * $searchFilter = new Like('product_name', new StringValue());
 * $result = $searchFilter->withValue('iPhone'); // Matches "iPhone 15", "Apple iPhone", etc.
 *
 * @example
 * // Email domain filtering (ends with pattern)
 * $emailFilter = new Like('email', new StringValue(), '%%%s');
 * $result = $emailFilter->withValue('@company.com'); // Matches emails ending with @company.com
 *
 * @example
 * // Title prefix filtering (starts with pattern)
 * $titleFilter = new Like('title', new StringValue(), '%s%%');
 * $result = $titleFilter->withValue('How to'); // Matches "How to code", "How to cook", etc.
 *
 * @example
 * // Fixed pattern search
 * $codeFilter = new Like('product_code', 'ABC', 'SKU-%s-%%');
 * // Matches "SKU-ABC-001", "SKU-ABC-premium", etc.
 *
 * @example
 * // Case-insensitive search (depends on database)
 * $nameFilter = new Like('customer_name', new StringValue());
 * $result = $nameFilter->withValue('john'); // May match "John", "JOHN", "john"
 *
 * @example
 * // Address search
 * $addressFilter = new Like('address', new StringValue());
 * $result = $addressFilter->withValue('Main St'); // Matches addresses containing "Main St"
 *
 * @example
 * // File name filtering
 * $fileFilter = new Like('filename', new StringValue(), '%%%s.pdf');
 * $result = $fileFilter->withValue('report'); // Matches "monthly-report.pdf", "sales-report.pdf"
 *
 * @example
 * // Log analysis
 * $logFilter = new Like('message', new StringValue());
 * $result = $logFilter->withValue('ERROR'); // Find error messages in logs
 */
class Like extends Expression
{
    /**
     * @param string $expression The field name to filter on
     * @param mixed $value Either fixed value or ValueInterface for dynamic input (defaults to StringValue)
     * @param string $pattern The pattern template where %s is replaced with the search value
     */
    public function __construct(
        string $expression,
        mixed $value = null,
        private readonly string $pattern = '%%%s%%',
    ) {
        parent::__construct($expression, $value ?? new StringValue());
    }

    /**
     * Get the pattern template used for matching.
     */
    public function getPattern(): string
    {
        return $this->pattern;
    }
}
