<?php

declare(strict_types=1);

namespace Spiral\DataGrid\Specification\Sorter;

/**
 * Always sorts in descending order (highest to lowest, Z-A, newest to oldest).
 * This sorter cannot be changed by user input - it's fixed to descending direction.
 *
 * Real-world usage examples:
 * - Latest content first: Always show newest posts, articles, or updates first
 * - High-value items: Always show most expensive products or highest amounts first
 * - Popularity rankings: Always show most popular items first (highest scores)
 * - Recent activity: Always show most recent user activity or logs first
 * - Top performers: Always show highest-rated, best-selling, or top-scoring items
 * - Reverse chronological: Always show latest dates first (news feeds, timelines)
 * - Leaderboards: Always show highest scores first
 * - Priority sorting: Always show highest priority items first
 *
 * Common descending sort patterns:
 * - Numbers: 100, 90, 80, 70, 60...
 * - Letters: Z, Y, X, W, V...
 * - Dates: 2024-12-31, 2024-12-30, 2024-12-29...
 * - Prices: $1000, $800, $600, $400...
 * - Scores: 95%, 87%, 72%, 65%...
 * - Counts: 1000 views, 500 views, 100 views...
 *
 * @example
 * // Always show newest articles first
 * $dateSort = new DescSorter('published_at');
 *
 * @example
 * // Always show most expensive products first
 * $priceSort = new DescSorter('price');
 *
 * @example
 * // Always show most popular content first (highest view count)
 * $popularitySort = new DescSorter('view_count');
 *
 * @example
 * // Always show highest-rated items first
 * $ratingSort = new DescSorter('average_rating');
 *
 * @example
 * // Multi-field descending sort (by score, then by completion time)
 * $leaderboardSort = new DescSorter('score', 'completion_time');
 *
 * @example
 * // Always show most recent user activity first
 * $activitySort = new DescSorter('last_login_at');
 *
 * @example
 * // Always show best-selling products first
 * $salesSort = new DescSorter('sales_count', 'revenue');
 *
 * @example
 * // Always show largest file sizes first
 * $sizeSort = new DescSorter('file_size_bytes');
 *
 * @example
 * // Usage in grid schema (fixed descending order)
 * $schema->addSorter('latest', new DescSorter('created_at'));
 * // User cannot change direction - always newest first
 *
 * @example
 * // News feed sorting (always newest first)
 * $newsSort = new DescSorter('published_at', 'priority_score');
 *
 * @example
 * // Performance metrics (always highest performance first)
 * $performanceSort = new DescSorter('response_time_ms', 'throughput');
 */
final class DescSorter extends AbstractSorter
{
    public function getValue(): string
    {
        return self::DESC;
    }
}
