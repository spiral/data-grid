<?php

declare(strict_types=1);

namespace Spiral\DataGrid\Specification\Sorter;

use Spiral\DataGrid\Specification\SorterInterface;
use Spiral\DataGrid\SpecificationInterface;

/**
 * Combines multiple sorters into a single set, applying the same direction to all sorters.
 * This is useful when you want to group different sorting strategies that should all
 * use the same direction when applied.
 *
 * When to use SorterSet:
 * - Multiple independent sorting approaches need same direction
 * - Complex business rules require multiple sorting strategies
 * - Different sorters for different aspects of the same data
 * - Fallback or alternative sorting methods
 *
 * ```
 * // E-commerce product sorting with multiple strategies
 * $productSorterSet = new SorterSet(
 *     new AscSorter('category'),              // Always sort by category first
 *     new DescSorter('featured'),             // Then by featured status
 *     new Sorter('price'),                    // Then by price (user direction)
 *     new Sorter('rating')                    // Finally by rating (user direction)
 * );
 *
 * // When user chooses 'asc':
 * $ascending = $productSorterSet->withDirection('asc');
 * // Results in: category ASC, featured DESC, price ASC, rating ASC
 *
 * // When user chooses 'desc':
 * $descending = $productSorterSet->withDirection('desc');
 * // Results in: category ASC, featured DESC, price DESC, rating DESC
 * ```
 * ```
 * // User management with multiple sorting criteria
 * $userSorterSet = new SorterSet(
 *     new AscSorter('department'),            // Always department first
 *     new Sorter('last_name', 'first_name'), // Name sorting (user direction)
 *     new Sorter('hire_date')                 // Hire date (user direction)
 * );
 * ```
 * ```
 * // Content management with business rules
 * $contentSorterSet = new SorterSet(
 *     new DescSorter('is_pinned'),           // Pinned content always first
 *     new AscSorter('category'),             // Then by category
 *     new Sorter('published_at'),            // Then by publish date (user direction)
 *     new Sorter('title')                    // Finally by title (user direction)
 * );
 * ```
 * ```
 * // Analytics dashboard with multiple metrics
 * $metricsSorterSet = new SorterSet(
 *     new DescSorter('priority'),            // High priority metrics first
 *     new Sorter('response_time'),           // Response time (user direction)
 *     new Sorter('error_rate'),              // Error rate (user direction)
 *     new Sorter('throughput')               // Throughput (user direction)
 * );
 * ```
 * ```
 * // Order management with business priorities
 * $orderSorterSet = new SorterSet(
 *     new DescSorter('is_urgent'),           // Urgent orders always first
 *     new AscSorter('ship_date'),            // Then by ship date
 *     new Sorter('total_amount'),            // Then by amount (user direction)
 *     new Sorter('customer_tier')            // Finally by customer tier (user direction)
 * );
 * ```
 * ```
 * // Search results with relevance and user preferences
 * $searchSorterSet = new SorterSet(
 *     new DescSorter('relevance_score'),     // Always most relevant first
 *     new Sorter('created_at'),              // Date sorting (user direction)
 *     new Sorter('popularity_score')         // Popularity (user direction)
 * );
 * ```
 * ```
 * // Task management with priority and flexibility
 * $taskSorterSet = new SorterSet(
 *     new AscSorter('status'),               // Status order: todo, in_progress, done
 *     new DescSorter('priority'),            // High priority first
 *     new Sorter('due_date'),                // Due date (user direction)
 *     new Sorter('estimated_hours')          // Effort estimate (user direction)
 * );
 * ```
 * ```
 * // Usage in grid schema
 * $schema->addSorter('comprehensive', new SorterSet(
 *     new DescSorter('featured'),            // Featured items first
 *     new Sorter('price'),                   // Price (user controllable)
 *     new Sorter('rating'),                  // Rating (user controllable)
 *     new AscSorter('name')                  // Name alphabetically
 * ));
 *
 * // User can then request:
 * // ?sort[comprehensive]=asc   (price ASC, rating ASC, others fixed)
 * // ?sort[comprehensive]=desc  (price DESC, rating DESC, others fixed)
 * ```
 * ```
 * // Performance-optimized sorting with fallbacks
 * $optimizedSorterSet = new SorterSet(
 *     new DescSorter('cached_score'),        // Use cached score if available
 *     new Sorter('calculated_score'),        // Fallback to calculated score
 *     new AscSorter('id')                    // Final tiebreaker
 * );
 * ```
 */
final class SorterSet implements SorterInterface
{
    private array $sorters;

    /**
     * @param SorterInterface ...$sorters Multiple sorters to combine into a set
     */
    public function __construct(SorterInterface ...$sorters)
    {
        $this->sorters = $sorters;
    }

    public function withDirection(int|string $direction): SpecificationInterface
    {
        $sorter = clone $this;
        $sorter->sorters = [];

        foreach ($this->sorters as $s) {
            $sorter->sorters[] = $s->withDirection($direction);
        }

        return $sorter;
    }

    /**
     * Get all sorters in this set.
     *
     * @return SorterInterface[]
     */
    public function getSorters(): array
    {
        return $this->sorters;
    }

    /**
     * Returns a placeholder value since this is a composite sorter.
     * Individual sorters in the set have their own values.
     */
    public function getValue(): string
    {
        return '1';
    }
}
