<?php

declare(strict_types=1);

namespace Spiral\DataGrid\Specification\Filter;

use Spiral\DataGrid\Specification\FilterInterface;
use Spiral\DataGrid\Specification\SequenceInterface;
use Spiral\DataGrid\SpecificationInterface;

/**
 * Combines filtering and sorting operations into a single specification.
 * This is useful for predefined filter-sort combinations that should be applied together.
 *
 * ```
 * // Popular posts filter-sort combination
 * $popularPosts = new SortedFilter(
 *     'popular_posts',
 *     new Gte('view_count', 1000),           // Filter: views >= 1000
 *     new DescSorter('popularity_score')     // Sort: by popularity descending
 * );
 * ```
 * ```
 * // Recent high-rated products
 * $topRecentProducts = new SortedFilter(
 *     'top_recent',
 *     new All(                               // Filter: recent AND highly rated
 *         new Gte('created_at', '-30 days'),
 *         new Gte('rating', 4.0)
 *     ),
 *     new DescSorter('rating')               // Sort: by rating descending
 * );
 * ```
 * ```
 * // Sale items sorted by discount
 * $bestDeals = new SortedFilter(
 *     'best_deals',
 *     new Gt('discount_percentage', 20),     // Filter: discount > 20%
 *     new DescSorter('discount_percentage')  // Sort: biggest discounts first
 * );
 * ```
 * ```
 * // New premium content
 * $premiumNew = new SortedFilter(
 *     'premium_new',
 *     new All(                               // Filter: premium AND recent
 *         new Equals('content_tier', 'premium'),
 *         new Gte('published_at', '-7 days')
 *     ),
 *     new DescSorter('published_at')         // Sort: newest first
 * );
 * ```
 * ```
 * // Urgent high-priority orders
 * $urgentOrders = new SortedFilter(
 *     'urgent_orders',
 *     new All(                               // Filter: urgent AND high value
 *         new Equals('priority', 'urgent'),
 *         new Gte('total_amount', 500)
 *     ),
 *     new AscSorter('deadline_date')         // Sort: most urgent deadline first
 * );
 * ```
 * ```
 * // Usage in grid schema
 * $schema->addFilter('popular', new Select([
 *     'trending' => new SortedFilter(
 *         'trending',
 *         new Gte('engagement_score', 100),
 *         new DescSorter('created_at')
 *     ),
 *     'bestselling' => new SortedFilter(
 *         'bestselling',
 *         new Gt('sales_count', 50),
 *         new DescSorter('sales_count')
 *     )
 * ]));
 * ```
 */
class SortedFilter implements SequenceInterface, FilterInterface
{
    /** @var SpecificationInterface[] */
    private readonly array $specifications;

    /**
     * @param string $value Identifier for this filter-sort combination
     * @param SpecificationInterface ...$specifications Filter and sorter specifications to apply together
     */
    public function __construct(
        private readonly string $value,
        SpecificationInterface ...$specifications,
    ) {
        $this->specifications = $specifications;
    }

    /**
     * Get all specifications (filters and sorters) to be applied.
     *
     * @return SpecificationInterface[]
     */
    public function getSpecifications(): array
    {
        return $this->specifications;
    }

    /**
     * Get the identifier value for this filter-sort combination.
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * This filter doesn't accept dynamic values - it's a predefined combination.
     */
    public function withValue(mixed $value): ?SpecificationInterface
    {
        return $this;
    }
}
