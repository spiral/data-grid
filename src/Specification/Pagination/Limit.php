<?php

declare(strict_types=1);

namespace Spiral\DataGrid\Specification\Pagination;

use Spiral\DataGrid\SpecificationInterface;

/**
 * Specifies the maximum number of records to return from a query.
 * This is typically used in conjunction with Offset to implement pagination.
 *
 * Real-world usage examples:
 * - Page size control: Limit results to 20 items per page
 * - Performance optimization: Prevent loading thousands of records at once
 * - API rate limiting: Control response size for mobile/bandwidth constraints
 * - User experience: Manageable chunks of data for table displays
 * - Memory management: Prevent server memory exhaustion
 * - Database optimization: Reduce query execution time and resource usage
 * - Mobile applications: Smaller result sets for slower connections
 * - Infinite scroll: Load fixed-size batches as user scrolls
 *
 * Common limit values:
 * - Small lists: 10-25 items (detailed views, complex data)
 * - Standard tables: 25-50 items (most common for web interfaces)
 * - Large datasets: 100-200 items (simple data, fast connections)
 * - API endpoints: 50-100 items (balance between requests and response size)
 * - Mobile: 10-20 items (slower connections, smaller screens)
 * - Reports: 500-1000 items (when pagination is less important)
 *
 * @example
 * // Standard page size for web table
 * $limit = new Limit(25); // Show 25 records per page
 *
 * @example
 * // Mobile-optimized smaller batches
 * $mobileLimit = new Limit(10); // Smaller chunks for mobile
 *
 * @example
 * // Large dataset with bigger pages
 * $bulkLimit = new Limit(100); // For data export or bulk operations
 *
 * @example
 * // API response size control
 * $apiLimit = new Limit(50); // Balanced API response size
 *
 * @example
 * // Performance-critical scenarios
 * $fastLimit = new Limit(15); // Optimized for fast response times
 *
 * @example
 * // Report generation
 * $reportLimit = new Limit(500); // Larger chunks for reports
 *
 * @example
 * // Usage with Offset for pagination
 * // Page 1: Offset(0) + Limit(20) = records 1-20
 * // Page 2: Offset(20) + Limit(20) = records 21-40
 * // Page 3: Offset(40) + Limit(20) = records 41-60
 * $page2 = [new Offset(20), new Limit(20)];
 *
 * @example
 * // Dynamic limit based on user preference
 * $userPreferredLimit = new Limit($userSettings['itemsPerPage']); // User choice: 10, 25, 50, 100
 *
 * @example
 * // Context-specific limits
 * $searchLimit = new Limit(30);      // Search results
 * $browseLimit = new Limit(48);      // Product browsing (grid layout)
 * $listLimit = new Limit(25);        // List views
 * $adminLimit = new Limit(100);      // Admin interfaces (more data)
 *
 * @example
 * // Database performance considerations
 * // Small limit for complex queries with joins
 * $complexQueryLimit = new Limit(10);
 *
 * // Larger limit for simple queries
 * $simpleQueryLimit = new Limit(100);
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
