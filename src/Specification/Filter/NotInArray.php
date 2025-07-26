<?php

declare(strict_types=1);

namespace Spiral\DataGrid\Specification\Filter;

use Spiral\DataGrid\Specification\Value\ArrayValue;
use Spiral\DataGrid\Specification\ValueInterface;

/**
 * Filters records where a field value does NOT exist within a specified array of values.
 * This is the inverse of InArray - excludes matching records instead of including them.
 *
 * Real-world usage examples:
 * - Exclude categories: Find products NOT in ['discontinued', 'recalled', 'seasonal']
 * - Status exclusion: Find orders NOT in ['cancelled', 'refunded', 'failed']
 * - Content filtering: Find articles NOT tagged with ['private', 'draft', 'archived']
 * - User filtering: Find users NOT in roles ['banned', 'suspended', 'deleted']
 * - Location exclusion: Find stores NOT in restricted cities ['City1', 'City2']
 * - ID exclusion: Find records NOT in blacklisted IDs [1, 5, 10, 15]
 * - Permission filtering: Find users NOT in restricted groups
 * - Quality control: Find products NOT in defective batches
 *
 * @example
 * // Exclude problematic product categories
 * $categoryFilter = new NotInArray('category', new StringValue());
 * $result = $categoryFilter->withValue(['discontinued', 'recalled', 'damaged']);
 *
 * @example
 * // Exclude problematic order statuses
 * $statusFilter = new NotInArray('status', new StringValue());
 * $result = $statusFilter->withValue(['cancelled', 'refunded', 'failed']);
 *
 * @example
 * // Exclude specific user roles
 * $roleFilter = new NotInArray('role', new StringValue());
 * $result = $roleFilter->withValue(['banned', 'suspended', 'guest']);
 *
 * @example
 * // Fixed blacklist of IDs
 * $idFilter = new NotInArray('user_id', [1, 5, 10, 15, 20]);
 *
 * @example
 * // Exclude content with specific tags
 * $tagFilter = new NotInArray('tags', new ArrayValue(new StringValue()));
 * $result = $tagFilter->withValue(['private', 'draft', 'archived']);
 *
 * @example
 * // Geographic exclusions
 * $locationFilter = new NotInArray('city', new StringValue());
 * $result = $locationFilter->withValue(['Restricted City 1', 'Restricted City 2']);
 *
 * @example
 * // Exclude low priority levels
 * $priorityFilter = new NotInArray('priority', new IntValue());
 * $result = $priorityFilter->withValue([0, 1]); // Exclude low/no priority items
 *
 * @example
 * // Quality control - exclude defective batches
 * $batchFilter = new NotInArray('batch_number', new StringValue());
 * $result = $batchFilter->withValue(['BATCH001', 'BATCH005', 'BATCH012']);
 */
class NotInArray extends Expression
{
    /**
     * @param string $expression The field name to filter on
     * @param mixed $value Either fixed array of values or ValueInterface for dynamic input
     * @param bool $wrapInArray Whether to automatically wrap ValueInterface in ArrayValue
     */
    public function __construct(string $expression, mixed $value, bool $wrapInArray = true)
    {
        parent::__construct(
            $expression,
            $value instanceof ValueInterface && $wrapInArray ? new ArrayValue($value) : $value,
        );
    }
}
