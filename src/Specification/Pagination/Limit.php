<?php

declare(strict_types=1);

namespace Spiral\DataGrid\Specification\Pagination;

use Spiral\DataGrid\SpecificationInterface;

/**
 * Specifies the maximum number of records to return from a query.
 * This is typically used in conjunction with Offset to implement pagination.
 *
 * ```
 * // Standard page size for web table
 * $limit = new Limit(25); // Show 25 records per page
 * ```
 *
 * @see Offset For specifying which records to skip
 * @see PagePaginator For complete page-based pagination
 */
final class Limit implements SpecificationInterface
{
    /**
     * @param int $value Maximum number of records to return (must be positive)
     */
    public function __construct(
        private readonly int $value,
    ) {}

    /**
     * Get the limit value.
     *
     * @return int Maximum number of records to return
     */
    public function getValue(): int
    {
        return $this->value;
    }
}
