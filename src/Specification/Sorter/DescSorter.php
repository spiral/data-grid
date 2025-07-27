<?php

declare(strict_types=1);

namespace Spiral\DataGrid\Specification\Sorter;

/**
 * Always sorts in descending order (highest to lowest, Z-A, newest to oldest).
 * This sorter cannot be changed by user input - it's fixed to descending direction.
 *
 * ```
 * // Always show newest articles first
 * $dateSort = new DescSorter('published_at');
 * ```
 * ```
 * // Multi-field descending sort (by score, then by completion time)
 * $leaderboardSort = new DescSorter('score', 'completion_time');
 * ```
 * ```
 * // Usage in grid schema (fixed descending order)
 * $schema->addSorter('latest', new DescSorter('created_at'));
 * // User cannot change direction - always newest first
 * ```
 */
final class DescSorter extends AbstractSorter
{
    public function getValue(): string
    {
        return self::DESC;
    }
}
