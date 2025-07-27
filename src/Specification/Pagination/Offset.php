<?php

declare(strict_types=1);

namespace Spiral\DataGrid\Specification\Pagination;

use Spiral\DataGrid\SpecificationInterface;

/**
 * Specifies how many records to skip before returning results.
 * This is typically used with Limit to implement pagination by calculating
 * which records to skip based on the current page and page size.
 *
 * ```
 * // Navigate to page 2 with 25 items per page
 * $page2Offset = new Offset(25); // Skip first 25 records
 * ```
 * ```
 * // API pagination example
 * // GET /api/users?page=4&limit=30
 * $apiOffset = new Offset(90); // Skip first 90 users (30 * 3)
 * $apiLimit = new Limit(30);   // Show next 30 users
 * ```
 *
 * @see Limit For specifying maximum number of records to return
 * @see PagePaginator For complete page-based pagination logic
 */
final class Offset implements SpecificationInterface
{
    /**
     * @param int $value Number of records to skip (must be non-negative)
     */
    public function __construct(
        private readonly int $value,
    ) {}

    /**
     * Get the offset value.
     *
     * @return int Number of records to skip
     */
    public function getValue(): int
    {
        return $this->value;
    }
}
