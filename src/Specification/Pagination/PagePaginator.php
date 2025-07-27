<?php

declare(strict_types=1);

namespace Spiral\DataGrid\Specification\Pagination;

use Spiral\DataGrid\Specification\FilterInterface;
use Spiral\DataGrid\Specification\SequenceInterface;
use Spiral\DataGrid\Specification\Value;
use Spiral\DataGrid\Specification\Value\EnumValue;
use Spiral\DataGrid\SpecificationInterface;

/**
 * Complete page-based pagination system that handles page numbers, page sizes,
 * and automatically converts them to Limit and Offset specifications.
 * This is the most user-friendly way to implement pagination in web applications.
 *
 * Features:
 * - Automatic Limit/Offset calculation
 * - Configurable default page size
 * - Restricted page size options (security/performance)
 * - User-friendly page number interface (starts from 1, not 0)
 * - Automatic bounds checking (prevents negative pages)
 *
 * ```
 * // Basic pagination: 20 items per page, allow 10/20/50/100 options
 * $paginator = new PagePaginator(20, [10, 20, 50, 100]);
 *
 * // User wants page 3 with 50 items per page
 * $result = $paginator->withValue(['page' => 3, 'limit' => 50]);
 * // Generates: Limit(50) + Offset(100) = records 101-150
 * ```
 *
 * ```
 * // Usage with user input
 * $paginator = new PagePaginator(20, [10, 20, 50, 100]);
 *
 * // Different user input scenarios:
 *
 * // Just page number (uses default limit)
 * $page2 = $paginator->withValue(['page' => 2]);
 * // Result: page=2, limit=20, offset=20, specifications=[Limit(20), Offset(20)]
 *
 * // Custom page size
 * $page3Large = $paginator->withValue(['page' => 3, 'limit' => 50]);
 * // Result: page=3, limit=50, offset=100, specifications=[Limit(50), Offset(100)]
 *
 * // Invalid page size (fallback to default)
 * $invalidLimit = $paginator->withValue(['page' => 1, 'limit' => 75]); // 75 not allowed
 * // Result: page=1, limit=20 (default), offset=0
 *
 * // Invalid page number (minimum 1)
 * $invalidPage = $paginator->withValue(['page' => 0]);
 * // Result: page=1 (corrected), limit=20, offset=0
 * ```
 */
final class PagePaginator implements SequenceInterface, FilterInterface
{
    private readonly EnumValue $limitValue;
    private int $page = 1;

    /**
     * @param int $limit Default number of items per page
     * @param array $allowedLimits Array of allowed page sizes (for security/performance)
     */
    public function __construct(
        private int $limit,
        array $allowedLimits = [],
    ) {
        $allowedLimits[] = $limit;

        \sort($allowedLimits);

        $this->limitValue = new EnumValue(new Value\IntValue(), ...$allowedLimits);
    }

    public function withValue(mixed $value): ?SpecificationInterface
    {
        $paginator = clone $this;
        if (!\is_array($value)) {
            return $paginator;
        }

        if (isset($value['limit']) && $paginator->limitValue->accepts($value['limit'])) {
            $paginator->limit = $paginator->limitValue->convert($value['limit']);
        }

        if (isset($value['page']) && \is_numeric($value['page'])) {
            $paginator->page = \max((int) $value['page'], 1);
        }

        return $paginator;
    }

    /**
     * Get the pagination specifications (Limit and Offset) to apply to the query.
     *
     * @return SpecificationInterface[] Array containing Limit and optionally Offset
     */
    public function getSpecifications(): array
    {
        $specifications = [new Limit($this->limit)];
        if ($this->page > 1) {
            $specifications[] = new Offset($this->limit * ($this->page - 1));
        }

        return $specifications;
    }

    /**
     * Get the current pagination state.
     *
     * @return array Current page and limit values ['limit' => int, 'page' => int]
     */
    public function getValue(): array
    {
        return [
            'limit' => $this->limit,
            'page' => $this->page,
        ];
    }
}
