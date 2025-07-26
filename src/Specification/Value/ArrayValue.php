<?php

declare(strict_types=1);

namespace Spiral\DataGrid\Specification\Value;

use Spiral\DataGrid\Specification\ValueInterface;

/**
 * Validates and converts arrays where each element matches a base value type.
 * Ensures all array elements are of the same validated type and the array is not empty.
 *
 * Real-world usage examples:
 * - Multi-select filters: User selects multiple categories, tags, or options
 * - Batch operations: Process multiple IDs, usernames, or product codes at once
 * - Tag systems: Validate arrays of tags, keywords, or labels
 * - Permissions: Handle multiple role IDs or permission names
 * - Geographic data: Arrays of coordinates, regions, or location IDs
 * - E-commerce: Multiple product IDs, category IDs, or attribute values
 * - Search filters: Multiple search terms or filter criteria
 * - Form inputs: Checkbox groups, multi-select dropdowns, tag inputs
 *
 * @example
 * // Multi-select category filter
 * $categoryFilter = new InArray('category', new ArrayValue(new StringValue()));
 * $result = $categoryFilter->withValue(['electronics', 'computers', 'mobile']);
 * // Validates each category name is a valid string
 *
 * @example
 * // Batch user ID processing
 * $userIdsValue = new ArrayValue(new IntValue());
 * $userIdsValue->accepts([1, 2, 3, 4]); // true - all integers
 * $userIdsValue->accepts(['1', '2', '3']); // true - converts strings to integers
 * $userIdsValue->accepts([1, 'invalid', 3]); // false - 'invalid' can't be integer
 *
 * @example
 * // Tag validation system
 * $tagValue = new ArrayValue(new StringValue());
 * $tagFilter = new InArray('tags', $tagValue);
 * $result = $tagFilter->withValue(['php', 'programming', 'tutorial']);
 * // Each tag validated as string
 *
 * @example
 * // E-commerce product selection
 * $productIdsValue = new ArrayValue(new IntValue());
 * $cartFilter = new InArray('product_ids', $productIdsValue);
 * $result = $cartFilter->withValue([101, 205, 308]); // Product IDs
 *
 * @example
 * // Permission management
 * $permissionValue = new ArrayValue(new EnumValue(
 *     new StringValue(), 'read', 'write', 'admin', 'moderate'
 * ));
 * $roleFilter = new InArray('permissions', $permissionValue);
 * $result = $roleFilter->withValue(['read', 'write']); // Valid permissions
 *
 * @example
 * // Geographic region filtering
 * $regionValue = new ArrayValue(new StringValue());
 * $locationFilter = new InArray('regions', $regionValue);
 * $result = $locationFilter->withValue(['north-america', 'europe', 'asia']);
 *
 * @example
 * // Numeric array validation
 * $scoresValue = new ArrayValue(new FloatValue());
 * $scoresValue->accepts([85.5, 92.3, 78.1]); // true
 * $scoresValue->accepts(['85.5', '92.3']); // true - converts strings to floats
 * $scoresValue->convert(['85.5', '92.3']); // Returns [85.5, 92.3]
 *
 * @example
 * // Email array validation
 * $emailValue = new ArrayValue(new RegexValue('/^[^@]+@[^@]+\.[^@]+$/'));
 * $emailFilter = new InArray('email_addresses', $emailValue);
 * $result = $emailFilter->withValue(['user1@example.com', 'user2@example.com']);
 *
 * @example
 * // Complex nested validation
 * $complexValue = new ArrayValue(new EnumValue(
 *     new IntValue(), 1, 2, 3, 4, 5 // Only ratings 1-5 allowed
 * ));
 * $ratingsFilter = new InArray('ratings', $complexValue);
 * $result = $ratingsFilter->withValue([4, 5, 3]); // Valid ratings
 *
 * @example
 * // Form checkbox processing
 * $interestsValue = new ArrayValue(new StringValue());
 * $userFilter = new Equals('interests', $interestsValue);
 * // From form: interests[] = ['technology', 'music', 'sports']
 * $result = $userFilter->withValue($_POST['interests']);
 *
 * @example
 * // API batch endpoints
 * $idsValue = new ArrayValue(new IntValue());
 * // POST /api/users/batch-update
 * // Body: {"user_ids": [1, 5, 10, 15]}
 * if ($idsValue->accepts($requestData['user_ids'])) {
 *     $validIds = $idsValue->convert($requestData['user_ids']);
 * }
 *
 * @example
 * // Search term arrays
 * $searchTermsValue = new ArrayValue(new StringValue());
 * $searchFilter = new All(
 *     new Like('title', $searchTermsValue),
 *     new Like('description', $searchTermsValue)
 * );
 * $result = $searchFilter->withValue(['php', 'tutorial', 'beginner']);
 *
 * Important notes:
 * - Empty arrays are rejected (returns false from accepts())
 * - All elements must pass the base value validation
 * - Nested ArrayValue instances are flattened to prevent infinite nesting
 * - Elements are converted using the base value's convert() method
 * - Maintains array order after conversion
 */
final class ArrayValue implements ValueInterface
{
    private readonly ValueInterface $base;

    public function __construct(ValueInterface $base)
    {
        $this->base = $base instanceof self ? $base->base : $base;
    }

    public function accepts(mixed $value): bool
    {
        if (!\is_array($value)) {
            return false;
        }

        foreach ($value as $child) {
            if (!$this->base->accepts($child)) {
                return false;
            }
        }

        return $value !== [];
    }

    public function convert(mixed $value): array
    {
        $result = [];
        foreach ($value as $child) {
            $result[] = $this->base->convert($child);
        }

        return $result;
    }
}
