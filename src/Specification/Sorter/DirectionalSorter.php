<?php

declare(strict_types=1);

namespace Spiral\DataGrid\Specification\Sorter;

use Spiral\DataGrid\Specification\SorterInterface;
use Spiral\DataGrid\SpecificationInterface;

/**
 * Provides directional sorting with two different sorters for ascending and descending directions.
 * This allows different fields or logic to be used for each direction, unlike regular Sorter
 * which uses the same fields for both directions.
 *
 * When to use DirectionalSorter vs regular Sorter:
 * - Use DirectionalSorter: When ASC and DESC need different fields/logic
 * - Use regular Sorter: When same fields used for both directions
 *
 * ```
 * // Different fields for different directions
 * $productSort = new DirectionalSorter(
 *     new AscSorter('name', 'sku'),        // ASC: Sort by name, then SKU
 *     new DescSorter('popularity', 'sales') // DESC: Sort by popularity, then sales
 * );
 * $ascResult = $productSort->withDirection('asc');   // Uses name, sku
 * $descResult = $productSort->withDirection('desc'); // Uses popularity, sales
 * ```
 * ```
 * // Time-based asymmetric sorting
 * $eventSort = new DirectionalSorter(
 *     new AscSorter('start_date'),         // ASC: Earliest events first
 *     new DescSorter('end_date')           // DESC: Latest ending events first
 * );
 * ```
 * ```
 * // Performance-optimized sorting
 * $userSort = new DirectionalSorter(
 *     new AscSorter('last_name', 'first_name'),  // ASC: Alphabetical by name
 *     new DescSorter('last_activity_at')         // DESC: Most active users first
 * );
 * ```
 * ```
 * // Content discovery patterns
 * $contentSort = new DirectionalSorter(
 *     new AscSorter('title'),                    // ASC: Alphabetical browsing
 *     new DescSorter('view_count', 'rating')     // DESC: Popular content first
 * );
 * ```
 * ```
 * // Financial data sorting
 * $transactionSort = new DirectionalSorter(
 *     new AscSorter('date', 'reference_number'), // ASC: Chronological with reference
 *     new DescSorter('amount', 'date')           // DESC: Largest amounts first
 * );
 * ```
 * ```
 * // E-commerce product discovery
 * $shopSort = new DirectionalSorter(
 *     new AscSorter('price', 'name'),            // ASC: Cheapest first, then name
 *     new DescSorter('rating', 'review_count')   // DESC: Best rated first
 * );
 * ```
 * ```
 * // Task management
 * $taskSort = new DirectionalSorter(
 *     new AscSorter('due_date', 'priority'),     // ASC: Earliest due date first
 *     new DescSorter('priority', 'created_at')   // DESC: Highest priority first
 * );
 * ```
 * ```
 * // Usage with user input
 * $sorter = new DirectionalSorter($ascSorter, $descSorter);
 *
 * // User wants ascending
 * $result = $sorter->withDirection('asc');    // Returns configured AscSorter
 * $result = $sorter->withDirection(1);        // Same as 'asc'
 * $result = $sorter->withDirection(SORT_ASC); // Same as 'asc'
 *
 * // User wants descending
 * $result = $sorter->withDirection('desc');    // Returns configured DescSorter
 * $result = $sorter->withDirection(-1);        // Same as 'desc'
 * $result = $sorter->withDirection(SORT_DESC); // Same as 'desc'
 *
 * // Invalid direction
 * $result = $sorter->withDirection('invalid'); // Returns null
 * ```
 */
final class DirectionalSorter implements SorterInterface
{
    private ?SorterInterface $sorter = null;
    private ?string $direction = null;

    /**
     * @param SorterInterface $asc Sorter to use for ascending direction
     * @param SorterInterface $desc Sorter to use for descending direction
     */
    public function __construct(
        private readonly SorterInterface $asc,
        private readonly SorterInterface $desc,
    ) {}

    public function withDirection(int|string $direction): ?SpecificationInterface
    {
        $sorter = clone $this;
        $sorter->direction = $sorter->checkDirection($direction);

        $sorter->sorter = match ($sorter->direction) {
            self::ASC => $sorter->asc->withDirection(self::ASC),
            self::DESC => $sorter->desc->withDirection(self::DESC),
            default => null,
        };

        return $sorter->sorter;
    }

    public function getValue(): ?string
    {
        return $this->direction;
    }

    /**
     * Normalize various direction input formats to standard ASC/DESC strings.
     *
     * @param int|string $direction User input direction
     * @return string|null Normalized direction or null if invalid
     */
    private function checkDirection(int|string $direction): ?string
    {
        return match (true) {
            \in_array($direction, ['-1', -1, SORT_DESC], true) => self::DESC,
            \in_array($direction, ['1', 1, SORT_ASC], true) => self::ASC,
            \is_string($direction) && \strtolower($direction) === self::DESC => self::DESC,
            \is_string($direction) && \strtolower($direction) === self::ASC => self::ASC,
            default => null,
        };
    }
}
