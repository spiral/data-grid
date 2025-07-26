<?php

declare(strict_types=1);

namespace Spiral\DataGrid\Specification\Filter;

use Spiral\DataGrid\Specification\Value\ArrayValue;
use Spiral\DataGrid\Specification\ValueInterface;

/**
 * Filters records where a field value exists within a specified array of values.
 * 
 * Real-world usage examples:
 * - Category filtering: Find products in specific categories like ['electronics', 'computers', 'mobile']
 * - Status filtering: Find orders with status in ['pending', 'processing', 'shipped']
 * - Tag filtering: Find articles with tags in ['php', 'programming', 'tutorial']
 * - User role filtering: Find users with roles in ['admin', 'moderator', 'editor']
 * - Location filtering: Find stores in cities ['New York', 'Los Angeles', 'Chicago']
 * - Multi-selection filters: Allow users to select multiple options from checkboxes
 * - ID filtering: Find records with IDs in [1, 5, 10, 15, 20]
 * 
 * @example
 * // Multi-category product filtering
 * $categoryFilter = new InArray('category', new StringValue());
 * $result = $categoryFilter->withValue(['electronics', 'computers', 'mobile']);
 * 
 * @example
 * // Order status filtering
 * $statusFilter = new InArray('status', new StringValue());
 * $result = $statusFilter->withValue(['pending', 'processing']);
 * 
 * @example
 * // Fixed list of valid IDs
 * $idFilter = new InArray('product_id', [1, 5, 10, 15, 20]);
 * 
 * @example
 * // Tag-based content filtering
 * $tagFilter = new InArray('tags', new ArrayValue(new StringValue()));
 * $result = $tagFilter->withValue(['php', 'tutorial', 'beginner']);
 * 
 * @example
 * // User permission filtering  
 * $roleFilter = new InArray('role', new EnumValue(
 *     new StringValue(), 'admin', 'moderator', 'editor', 'author'
 * ));
 * $result = $roleFilter->withValue(['admin', 'moderator']);
 * 
 * @example
 * // Geographic filtering
 * $locationFilter = new InArray('city', new StringValue());
 * $result = $locationFilter->withValue(['New York', 'Los Angeles', 'Chicago']);
 * 
 * @example
 * // Priority level filtering
 * $priorityFilter = new InArray('priority', new IntValue());
 * $result = $priorityFilter->withValue([1, 2, 3]); // High, Medium, Normal priorities
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
