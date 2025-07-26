<?php

declare(strict_types=1);

namespace Spiral\DataGrid\Specification\Pagination;

use Spiral\DataGrid\SpecificationInterface;

/**
 * Specifies how many records to skip before returning results.
 * This is typically used with Limit to implement pagination by calculating
 * which records to skip based on the current page and page size.
 *
 * Real-world usage examples:
 * - Page navigation: Skip records to show specific page (page 3 = skip first 40 records)
 * - Database pagination: Efficient way to paginate through large datasets
 * - API pagination: RESTful APIs that support offset-based pagination
 * - Search results: Navigate through pages of search results
 * - Data browsing: Browse through product catalogs, user lists, articles
 * - Report pagination: Break large reports into manageable pages
 * - Table displays: Navigate through table data page by page
 * - Infinite scroll: Skip already loaded records when loading more
 *
 * Offset calculation patterns:
 * - Page 1: Offset(0) - no records skipped
 * - Page 2: Offset(pageSize * 1) - skip first page
 * - Page 3: Offset(pageSize * 2) - skip first two pages
 * - Page N: Offset(pageSize * (N-1)) - skip first N-1 pages
 *
 * Common offset scenarios:
 * - Page 1 of 20/page: Offset(0) + Limit(20) = records 1-20
 * - Page 2 of 20/page: Offset(20) + Limit(20) = records 21-40
 * - Page 3 of 20/page: Offset(40) + Limit(20) = records 41-60
 * - Page 5 of 50/page: Offset(200) + Limit(50) = records 201-250
 *
 * @example
 * // Navigate to page 2 with 25 items per page
 * $page2Offset = new Offset(25); // Skip first 25 records
 *
 * @example
 * // Navigate to page 5 with 20 items per page
 * $page5Offset = new Offset(80); // Skip first 80 records (20 * 4)
 *
 * @example
 * // Large dataset navigation
 * $offset = new Offset(1000); // Skip first 1000 records for page 11 (100/page)
 *
 * @example
 * // Calculated offset for dynamic pagination
 * $pageNumber = 3;
 * $itemsPerPage = 25;
 * $calculatedOffset = new Offset(($pageNumber - 1) * $itemsPerPage); // Skip 50 records
 *
 * @example
 * // API pagination example
 * // GET /api/users?page=4&limit=30
 * $apiOffset = new Offset(90); // Skip first 90 users (30 * 3)
 * $apiLimit = new Limit(30);   // Show next 30 users
 *
 * @example
 * // Search results pagination
 * // Show page 2 of search results (15 results per page)
 * $searchOffset = new Offset(15); // Skip first 15 search results
 * $searchLimit = new Limit(15);   // Show next 15 results
 *
 * @example
 * // Zero offset for first page
 * $firstPageOffset = new Offset(0); // Start from the beginning
 *
 * @example
 * // Large report pagination
 * $reportPage = 10;
 * $recordsPerPage = 100;
 * $reportOffset = new Offset(900); // Skip 900 records for page 10
 *
 * @example
 * // Mobile pagination with smaller pages
 * $mobilePage = 8;
 * $mobilePageSize = 10;
 * $mobileOffset = new Offset(70); // Skip 70 records for mobile page 8
 *
 * @example
 * // Performance considerations
 * // Small offset for fast queries
 * $fastOffset = new Offset(0);     // First page loads fastest
 *
 * // Large offset may be slower on some databases
 * $largeOffset = new Offset(10000); // May require optimization
 *
 * @example
 * // Usage in pagination components
 * class PaginationHelper {
 *     public static function getOffset(int $page, int $pageSize): Offset {
 *         return new Offset(($page - 1) * $pageSize);
 *     }
 * }
 *
 * $offset = PaginationHelper::getOffset(5, 25); // Page 5, 25/page = skip 100
 *
 * @example
 * // Database query pattern
 * // SELECT * FROM products ORDER BY name LIMIT 20 OFFSET 40;
 * // Equivalent to: Offset(40) + Limit(20) for page 3
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
