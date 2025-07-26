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
 * Real-world usage examples:
 * - Web application pagination: "Page 3 of 15" with configurable page sizes
 * - E-commerce product listings: Browse products with "Show 20/50/100 per page" options
 * - Content management: Navigate through articles, posts, or media with page controls
 * - User management: Admin interfaces with paginated user lists
 * - Search results: Navigate through search results with page numbers
 * - API endpoints: RESTful pagination with page and limit parameters
 * - Data tables: Table pagination with customizable page sizes
 * - Mobile applications: Pagination optimized for mobile interfaces
 *
 * Features:
 * - Automatic Limit/Offset calculation
 * - Configurable default page size
 * - Restricted page size options (security/performance)
 * - User-friendly page number interface (starts from 1, not 0)
 * - Automatic bounds checking (prevents negative pages)
 *
 * @example
 * // Basic pagination: 20 items per page, allow 10/20/50/100 options
 * $paginator = new PagePaginator(20, [10, 20, 50, 100]);
 *
 * // User wants page 3 with 50 items per page
 * $result = $paginator->withValue(['page' => 3, 'limit' => 50]);
 * // Generates: Limit(50) + Offset(100) = records 101-150
 *
 * @example
 * // E-commerce product pagination
 * $productPaginator = new PagePaginator(24, [12, 24, 48, 96]);
 * // Default: 24 products per page (good for 4x6 or 3x8 grid)
 * // Options: 12 (3x4), 24 (4x6), 48 (6x8), 96 (8x12)
 *
 * @example
 * // Mobile-optimized pagination
 * $mobilePaginator = new PagePaginator(10, [5, 10, 20]);
 * // Smaller page sizes for mobile devices
 *
 * @example
 * // Admin interface pagination
 * $adminPaginator = new PagePaginator(50, [25, 50, 100, 200]);
 * // Larger page sizes for admin users who need to see more data
 *
 * @example
 * // API pagination
 * $apiPaginator = new PagePaginator(30, [10, 30, 50, 100]);
 * // Balanced for API responses
 *
 * @example
 * // Search results pagination
 * $searchPaginator = new PagePaginator(15, [10, 15, 30]);
 * // Optimized for search result scanning
 *
 * @example
 * // Content management pagination
 * $contentPaginator = new PagePaginator(25, [10, 25, 50]);
 * // Good for article/post listings
 *
 * @example
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
 *
 * @example
 * // Grid schema integration
 * $schema = new GridSchema();
 * $schema->setPaginator(new PagePaginator(25, [10, 25, 50, 100]));
 *
 * // URL parameters: ?paginate[page]=4&paginate[limit]=50
 * // Results in: Skip 150 records, show next 50 (records 151-200)
 *
 * @example
 * // Frontend pagination component data
 * $paginator = new PagePaginator(20, [10, 20, 50]);
 * $current = $paginator->withValue(['page' => 5, 'limit' => 20]);
 * $paginationData = $current->getValue();
 * // Returns: ['limit' => 20, 'page' => 5]
 * // Frontend can calculate: showing records 81-100
 *
 * @example
 * // Performance considerations
 *
 * // Fast pagination (small pages, limited options)
 * $fastPaginator = new PagePaginator(15, [10, 15, 25]);
 *
 * // Bulk data pagination (larger pages for efficiency)
 * $bulkPaginator = new PagePaginator(100, [50, 100, 200, 500]);
 *
 * // Memory-conscious pagination (prevent huge pages)
 * $safePaginator = new PagePaginator(25, [10, 25, 50]); // Max 50 items
 *
 * @example
 * // Advanced pagination patterns
 *
 * // Calculate total pages (requires total record count)
 * $totalRecords = 1247;
 * $pageSize = 25;
 * $totalPages = ceil($totalRecords / $pageSize); // 50 pages
 *
 * // Generate page links
 * for ($i = 1; $i <= $totalPages; $i++) {
 *     $pageLink = $paginator->withValue(['page' => $i, 'limit' => $pageSize]);
 *     // Generate UI for page $i
 * }
 *
 * @example
 * // Database query integration
 * $paginator = new PagePaginator(20, [10, 20, 50]);
 * $page = $paginator->withValue(['page' => 3, 'limit' => 20]);
 * $specs = $page->getSpecifications(); // [Limit(20), Offset(40)]
 *
 * // SQL equivalent: SELECT * FROM table ORDER BY id LIMIT 20 OFFSET 40;
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
