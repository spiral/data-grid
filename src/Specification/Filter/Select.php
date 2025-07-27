<?php

declare(strict_types=1);

namespace Spiral\DataGrid\Specification\Filter;

use Spiral\DataGrid\Specification\FilterInterface;
use Spiral\DataGrid\SpecificationInterface;

use function Spiral\DataGrid\hasValue;

/**
 * Provides filter selection from a predefined set of named filters.
 * Users can select one or multiple filters by providing their keys.
 * If multiple filters are selected, they are combined with AND logic.
 *
 * ```
 * // Predefined content filters
 * $contentFilter = new Select([
 *     'popular' => new Gte('view_count', 1000),
 *     'recent' => new Gte('created_at', '-7 days'),
 *     'featured' => new Equals('is_featured', true),
 *     'top_rated' => new Gte('rating', 4.5)
 * ]);
 *
 * // Select single filter
 * $result = $contentFilter->withValue('popular'); // Show popular content
 *
 * // Select multiple filters (AND logic)
 * $result = $contentFilter->withValue(['popular', 'recent']); // Popular AND recent
 * ```
 * ```
 * // E-commerce product filters
 * $productFilter = new Select([
 *     'on_sale' => new Gt('discount_percentage', 0),
 *     'in_stock' => new Gt('quantity', 0),
 *     'bestseller' => new Gte('sales_count', 100),
 *     'new_arrival' => new Gte('created_at', '-30 days'),
 *     'premium' => new Gte('price', 500)
 * ]);
 * $result = $productFilter->withValue('on_sale'); // Show sale items
 * ```
 * ```
 * // User management presets
 * $userFilter = new Select([
 *     'active' => new Equals('status', 'active'),
 *     'verified' => new Equals('email_verified', true),
 *     'premium' => new Equals('subscription_type', 'premium'),
 *     'new_members' => new Gte('created_at', '-30 days'),
 *     'power_users' => new Gte('login_count', 100)
 * ]);
 * $result = $userFilter->withValue(['active', 'verified']); // Active AND verified users
 * ```
 * ```
 * // Report time periods
 * $reportFilter = new Select([
 *     'today' => new Gte('date', 'today'),
 *     'week' => new Gte('date', '-7 days'),
 *     'month' => new Gte('date', '-30 days'),
 *     'quarter' => new Gte('date', '-90 days'),
 *     'year' => new Gte('date', '-365 days')
 * ]);
 * $result = $reportFilter->withValue('month'); // Last 30 days
 * ```
 * ```
 * // Complex business rules
 * $orderFilter = new Select([
 *     'vip_orders' => new All(
 *         new Gte('total_amount', 1000),
 *         new Equals('customer_tier', 'vip')
 *     ),
 *     'urgent_orders' => new All(
 *         new Equals('priority', 'urgent'),
 *         new Lt('days_until_deadline', 3)
 *     ),
 *     'international' => new NotEquals('shipping_country', 'US')
 * ]);
 * $result = $orderFilter->withValue('vip_orders'); // VIP customer orders
 * ```
 */
final class Select extends Group
{
    /**
     * @param array|FilterInterface[] $filters Associative array of filter names to FilterInterface instances
     */
    public function __construct(array $filters)
    {
        $this->filters = $filters;
    }

    public function withValue(mixed $value): ?SpecificationInterface
    {
        $select = $this->clone($value);
        $value = (array) $value;

        foreach ($this->filters as $name => $filter) {
            $name = (string) $name;
            if (!hasValue($value, $name)) {
                continue;
            }

            $select->filters[$name] = $filter;
        }

        if (empty($select->filters)) {
            return null;
        }

        $filters = \array_values($select->filters);

        return \count($filters) === 1 ? $filters[0] : new All(...$filters);
    }
}
