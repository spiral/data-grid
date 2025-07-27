<?php

declare(strict_types=1);

namespace Spiral\DataGrid\Specification\Sorter;

use Spiral\DataGrid\Specification\SequenceInterface;
use Spiral\DataGrid\Specification\SorterInterface;
use Spiral\DataGrid\SpecificationInterface;

/**
 * Combines filtering and sorting operations into a single specification.
 * This allows creating predefined filter-sort combinations that apply both
 * filtering criteria and sorting rules together.
 *
 * This is useful for:
 * - Predefined categories that need both filtering and sorting
 * - Business logic that requires specific filter-sort combinations
 * - User experience presets (like "Popular", "Trending", "New")
 * - Complex business rules that can't be separated into independent filter/sort
 *
 * ```
 * // "Trending products" - recent AND popular, sorted by engagement
 * $trendingProducts = new FilteredSorter(
 *     'trending',
 *     new All(                                  // Filter: Recent AND popular
 *         new Gte('created_at', '-30 days'),
 *         new Gte('view_count', 100)
 *     ),
 *     new DescSorter('engagement_score')        // Sort: By engagement
 * );
 * ```
 * ```
 * // "Premium deals" - premium products on sale, sorted by discount
 * $premiumDeals = new FilteredSorter(
 *     'premium_deals',
 *     new All(                                  // Filter: Premium AND on sale
 *         new Equals('tier', 'premium'),
 *         new Gt('discount_percentage', 0)
 *     ),
 *     new DescSorter('discount_percentage')     // Sort: Biggest discounts first
 * );
 * ```
 * ```
 * // "New arrivals" - recent products sorted by newest first
 * $newArrivals = new FilteredSorter(
 *     'new_arrivals',
 *     new Gte('created_at', '-14 days'),       // Filter: Last 2 weeks
 *     new DescSorter('created_at')              // Sort: Newest first
 * );
 * ```
 * ```
 * // "Top performers" - high-rated items sorted by performance metrics
 * $topPerformers = new FilteredSorter(
 *     'top_performers',
 *     new All(                                  // Filter: High rated AND high sales
 *         new Gte('rating', 4.0),
 *         new Gte('sales_count', 50)
 *     ),
 *     new DescSorter('performance_score')       // Sort: Best performance first
 * );
 * ```
 * ```
 * // "Staff favorites" - curated content sorted by recommendation strength
 * $staffFavorites = new FilteredSorter(
 *     'staff_favorites',
 *     new Equals('staff_recommended', true),    // Filter: Staff recommended
 *     new DescSorter('recommendation_score')    // Sort: Strongest recommendations first
 * );
 * ```
 * ```
 * // "Limited time offers" - time-sensitive deals sorted by urgency
 * $limitedOffers = new FilteredSorter(
 *     'limited_time',
 *     new All(                                  // Filter: Has expiration AND active
 *         new NotEquals('expires_at', null),
 *         new Gte('expires_at', 'now')
 *     ),
 *     new AscSorter('expires_at')               // Sort: Most urgent first
 * );
 * ```
 * ```
 * // Usage in Select filter for multiple presets
 * $presetFilter = new Select([
 *     'trending' => new FilteredSorter(
 *         'trending',
 *         new Gte('engagement_score', 100),
 *         new DescSorter('created_at')
 *     ),
 *     'popular' => new FilteredSorter(
 *         'popular',
 *         new Gte('view_count', 1000),
 *         new DescSorter('view_count')
 *     ),
 *     'recent' => new FilteredSorter(
 *         'recent',
 *         new Gte('created_at', '-7 days'),
 *         new DescSorter('created_at')
 *     )
 * ]);
 * ```
 * ```
 * // Complex business rule: VIP customer orders
 * $vipOrders = new FilteredSorter(
 *     'vip_orders',
 *     new All(                                  // Filter: VIP customers with large orders
 *         new Equals('customer_tier', 'vip'),
 *         new Gte('order_total', 500)
 *     ),
 *     new DescSorter('order_total', 'created_at') // Sort: Largest orders first
 * );
 * ```
 */
class FilteredSorter implements SequenceInterface, SorterInterface
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
     * Get all specifications (filters and sorters) to be applied together.
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
     * This sorter doesn't accept dynamic direction changes - it's a predefined combination.
     *
     * @param int|string $direction Ignored - direction is predetermined by the sorter specifications
     * @return SpecificationInterface Always returns $this since it's a fixed combination
     */
    public function withDirection(int|string $direction): ?SpecificationInterface
    {
        return $this;
    }
}
