<?php

declare(strict_types=1);

namespace Spiral\DataGrid\Specification\Sorter;

/**
 * Always sorts in ascending order (lowest to highest, A-Z, oldest to newest).
 * This sorter cannot be changed by user input - it's fixed to ascending direction.
 *
 * ```
 * // Always sort products by name alphabetically (A-Z)
 * $nameSort = new AscSorter('name');
 * ```
 * ```
 * // Employee hierarchy (by level, then by hire date)
 * $hierarchySort = new AscSorter('organization_level', 'hire_date');
 * ```
 * ```
 * // Sequential task ordering
 * $taskSort = new AscSorter('sequence_number', 'created_at');
 * ```
 * ```
 * // Usage in grid schema (fixed ascending order)
 * $schema->addSorter('name_asc', new AscSorter('name'));
 * // User cannot change direction - it's always A-Z
 * ```
 */
final class AscSorter extends AbstractSorter
{
    public function getValue(): string
    {
        return self::ASC;
    }
}
