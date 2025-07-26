<?php

declare(strict_types=1);

namespace Spiral\DataGrid\Specification\Filter;

use Spiral\DataGrid\Specification\FilterInterface;
use Spiral\DataGrid\SpecificationInterface;

/**
 * Combines multiple filters with AND logic - all filters must match.
 *
 * Real-world usage examples:
 * - E-commerce: Find products that are both "in stock" AND "price < 100" AND "category = electronics"
 * - User management: Find users who are both "active" AND "verified" AND "created in last month"
 * - Content filtering: Find articles that are both "published" AND "featured" AND "author = specific user"
 *
 * @example
 * // Find products that are electronics, under $100, and in stock
 * $filter = new All(
 *     new Equals('category', 'electronics'),
 *     new Lt('price', 100),
 *     new Gt('stock_quantity', 0)
 * );
 *
 * @example
 * // Find users with specific criteria
 * $filter = new All(
 *     new Equals('status', 'active'),
 *     new Equals('email_verified', true),
 *     new Gte('created_at', '2024-01-01')
 * );
 */
final class All extends Group
{
    public function __construct(FilterInterface ...$filter)
    {
        $this->filters = $filter;
    }

    public function withValue(mixed $value): ?SpecificationInterface
    {
        $all = $this->clone($value);
        foreach ($this->filters as $filter) {
            $applied = $filter->withValue($value);

            if ($applied === null) {
                // all nested filters must be configured
                return null;
            }

            $all->filters[] = $applied;
        }

        return !empty($all->filters) ? $all : null;
    }
}
