<?php

declare(strict_types=1);

namespace Spiral\DataGrid\Specification\Filter;

use Spiral\DataGrid\Specification\Value\ArrayValue;
use Spiral\DataGrid\Specification\ValueInterface;

/**
 * Filters records where a field value exists within a specified array of values.
 *
 * ```
 * // Multi-category product filtering
 * $categoryFilter = new InArray('category', new StringValue());
 * $result = $categoryFilter->withValue(['electronics', 'computers', 'mobile']);
 * ```
 * ```
 * // Order status filtering
 * $statusFilter = new InArray('status', new StringValue());
 * $result = $statusFilter->withValue(['pending', 'processing']);
 * ```
 * ```
 * // Fixed list of valid IDs
 * $idFilter = new InArray('product_id', [1, 5, 10, 15, 20]);
 * ```
 * ```
 * // Tag-based content filtering
 * $tagFilter = new InArray('tags', new ArrayValue(new StringValue()));
 * $result = $tagFilter->withValue(['php', 'tutorial', 'beginner']);
 * ```
 * ```
 * // User permission filtering
 * $roleFilter = new InArray('role', new EnumValue(
 *     new StringValue(), 'admin', 'moderator', 'editor', 'author'
 * ));
 * $result = $roleFilter->withValue(['admin', 'moderator']);
 * ```
 * ```
 * // Geographic filtering
 * $locationFilter = new InArray('city', new StringValue());
 * $result = $locationFilter->withValue(['New York', 'Los Angeles', 'Chicago']);
 * ```
 * ```
 * // Priority level filtering
 * $priorityFilter = new InArray('priority', new IntValue());
 * $result = $priorityFilter->withValue([1, 2, 3]); // High, Medium, Normal priorities
 * ```
 */
class InArray extends Expression
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
