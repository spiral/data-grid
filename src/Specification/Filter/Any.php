<?php

declare(strict_types=1);

namespace Spiral\DataGrid\Specification\Filter;

use Spiral\DataGrid\Specification\FilterInterface;
use Spiral\DataGrid\SpecificationInterface;

/**
 * Combines multiple filters with OR logic - at least one filter must match.
 *
 * Real-world usage examples:
 * - Search functionality: Find items that match "title contains keyword" OR "description contains keyword" OR "tags contain keyword"
 * - Status filtering: Find orders that are "pending" OR "processing" OR "shipped"
 * - Category browsing: Show products from "electronics" OR "computers" OR "mobile" categories
 * - User roles: Find users who are "admin" OR "moderator" OR "premium subscriber"
 *
 * @example
 * // Search across multiple fields
 * $searchFilter = new Any(
 *     new Like('title', new StringValue()),
 *     new Like('description', new StringValue()),
 *     new Like('tags', new StringValue())
 * );
 * $result = $searchFilter->withValue('iPhone'); // Matches any field containing "iPhone"
 *
 * @example
 * // Multiple status filtering
 * $statusFilter = new Any(
 *     new Equals('status', 'pending'),
 *     new Equals('status', 'processing'),
 *     new Equals('status', 'shipped')
 * );
 *
 * @example
 * // Flexible user permissions
 * $accessFilter = new Any(
 *     new Equals('role', 'admin'),
 *     new Equals('is_premium', true),
 *     new Gt('subscription_level', 2)
 * );
 */
final class Any extends Group
{
    public function __construct(FilterInterface ...$filter)
    {
        $this->filters = $filter;
    }

    public function withValue(mixed $value): ?SpecificationInterface
    {
        $any = $this->clone($value);
        foreach ($this->filters as $filter) {
            $applied = $filter->withValue($value);

            if ($applied === null) {
                // all nested filters must be configured
                continue;
            }

            $any->filters[] = $applied;
        }

        return !empty($any->filters) ? $any : null;
    }
}
