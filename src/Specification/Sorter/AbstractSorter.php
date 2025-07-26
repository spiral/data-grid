<?php

declare(strict_types=1);

namespace Spiral\DataGrid\Specification\Sorter;

use Spiral\DataGrid\Specification\SorterInterface;
use Spiral\DataGrid\SpecificationInterface;

/**
 * Abstract base class for all sorting specifications.
 * Provides common functionality for sorters that operate on field expressions
 * with predefined sorting directions.
 *
 * Real-world usage concepts:
 * - Single-direction sorting: Always ascending or always descending
 * - Fixed sorting rules: Business logic that requires specific sort order
 * - Default sort behaviors: Fallback sorting when no user preference is set
 * - Multi-field sorting: Sorting by multiple fields with same direction
 * - Data presentation: Consistent ordering for reports and listings
 *
 * Available directions (constants):
 * - SorterInterface::ASC = 'asc' (ascending order: 1, 2, 3... or A, B, C...)
 * - SorterInterface::DESC = 'desc' (descending order: 3, 2, 1... or Z, Y, X...)
 *
 * @example
 * // Custom sorter implementation
 * class CustomSorter extends AbstractSorter {
 *     public function getValue(): string {
 *         return self::ASC; // Always ascending
 *     }
 * }
 *
 * @example
 * // Multi-field sorting
 * $sorter = new CustomSorter('last_name', 'first_name', 'middle_name');
 * $expressions = $sorter->getExpressions(); // ['last_name', 'first_name', 'middle_name']
 *
 * @see AscSorter For always-ascending sorting
 * @see DescSorter For always-descending sorting
 * @see Sorter For user-controllable direction sorting
 */
abstract class AbstractSorter implements SorterInterface
{
    private readonly array $expressions;

    /**
     * @param string ...$expressions Field names to sort by (in order of priority)
     */
    public function __construct(string ...$expressions)
    {
        $this->expressions = $expressions;
    }

    /**
     * Direction is fixed for abstract sorters - they don't change based on user input.
     *
     * @param int|string $direction Ignored for abstract sorters (direction is predetermined)
     * @return SpecificationInterface Always returns $this since direction is fixed
     */
    public function withDirection(int|string $direction): SpecificationInterface
    {
        return $this;
    }

    /**
     * Get the field expressions this sorter operates on.
     *
     * @return string[] Array of field names in sort priority order
     */
    public function getExpressions(): array
    {
        return $this->expressions;
    }
}
