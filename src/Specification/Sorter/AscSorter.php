<?php

declare(strict_types=1);

namespace Spiral\DataGrid\Specification\Sorter;

/**
 * Always sorts in ascending order (lowest to highest, A-Z, oldest to newest).
 * This sorter cannot be changed by user input - it's fixed to ascending direction.
 *
 * Real-world usage examples:
 * - Alphabetical listings: Always show names A-Z
 * - Chronological order: Always show oldest events first
 * - Priority queues: Always show lowest priority numbers first (1, 2, 3...)
 * - Sequential data: Always show items in natural order (IDs, sequence numbers)
 * - Hierarchical data: Always show parent items before children
 * - Default sort orders: When business rules require consistent ascending order
 * - Price listings: Always show cheapest items first
 * - Age sorting: Always show youngest to oldest
 *
 * Common ascending sort patterns:
 * - Numbers: 1, 2, 3, 4, 5...
 * - Letters: A, B, C, D, E...
 * - Dates: 2020-01-01, 2020-01-02, 2020-01-03...
 * - Prices: $10, $20, $30, $40...
 * - Names: Alice, Bob, Charlie, David...
 *
 * @example
 * // Always sort products by name alphabetically (A-Z)
 * $nameSort = new AscSorter('name');
 *
 * @example
 * // Always sort events chronologically (oldest first)
 * $dateSort = new AscSorter('event_date');
 *
 * @example
 * // Always sort by priority (1=highest, 2=medium, 3=lowest)
 * $prioritySort = new AscSorter('priority_level');
 *
 * @example
 * // Multi-field ascending sort (last name, then first name)
 * $nameSort = new AscSorter('last_name', 'first_name');
 *
 * @example
 * // Always show cheapest products first
 * $priceSort = new AscSorter('price');
 *
 * @example
 * // Employee hierarchy (by level, then by hire date)
 * $hierarchySort = new AscSorter('organization_level', 'hire_date');
 *
 * @example
 * // Sequential task ordering
 * $taskSort = new AscSorter('sequence_number', 'created_at');
 *
 * @example
 * // Usage in grid schema (fixed ascending order)
 * $schema->addSorter('name_asc', new AscSorter('name'));
 * // User cannot change direction - it's always A-Z
 */
final class AscSorter extends AbstractSorter
{
    public function getValue(): string
    {
        return self::ASC;
    }
}
