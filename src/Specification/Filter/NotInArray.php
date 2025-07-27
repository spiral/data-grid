<?php

declare(strict_types=1);

namespace Spiral\DataGrid\Specification\Filter;

use Spiral\DataGrid\Specification\Value\ArrayValue;
use Spiral\DataGrid\Specification\ValueInterface;

/**
 * Filters records where a field value does NOT exist within a specified array of values.
 * This is the inverse of InArray - excludes matching records instead of including them.
 *
 * ```
 * // Exclude problematic product categories
 * $categoryFilter = new NotInArray('category', new StringValue());
 * $result = $categoryFilter->withValue(['discontinued', 'recalled', 'damaged']);
 * ```
 * ```
 * // Exclude problematic order statuses
 * $statusFilter = new NotInArray('status', new StringValue());
 * $result = $statusFilter->withValue(['cancelled', 'refunded', 'failed']);
 * ```
 * ```
 * // Exclude specific user roles
 * $roleFilter = new NotInArray('role', new StringValue());
 * $result = $roleFilter->withValue(['banned', 'suspended', 'guest']);
 * ```
 * ```
 * // Fixed blacklist of IDs
 * $idFilter = new NotInArray('user_id', [1, 5, 10, 15, 20]);
 * ```
 * ```
 * // Exclude content with specific tags
 * $tagFilter = new NotInArray('tags', new ArrayValue(new StringValue()));
 * $result = $tagFilter->withValue(['private', 'draft', 'archived']);
 * ```
 * ```
 * // Geographic exclusions
 * $locationFilter = new NotInArray('city', new StringValue());
 * $result = $locationFilter->withValue(['Restricted City 1', 'Restricted City 2']);
 * ```
 * ```
 * // Exclude low priority levels
 * $priorityFilter = new NotInArray('priority', new IntValue());
 * $result = $priorityFilter->withValue([0, 1]); // Exclude low/no priority items
 * ```
 * ```
 * // Quality control - exclude defective batches
 * $batchFilter = new NotInArray('batch_number', new StringValue());
 * $result = $batchFilter->withValue(['BATCH001', 'BATCH005', 'BATCH012']);
 * ```
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
