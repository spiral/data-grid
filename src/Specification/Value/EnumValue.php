<?php

declare(strict_types=1);

namespace Spiral\DataGrid\Specification\Value;

use Spiral\DataGrid\Exception\ValueException;
use Spiral\DataGrid\Specification\ValueInterface;

/**
 * Validates values against a predefined set of allowed values (enumeration).
 * Only accepts input that matches one of the specified enum values after type conversion.
 *
 * ```
 * // Order status validation
 * $statusValue = new EnumValue(
 *     new StringValue(),
 *     'pending', 'processing', 'shipped', 'delivered', 'cancelled'
 * );
 * $orderFilter = new Equals('status', $statusValue);
 * $result = $orderFilter->withValue('shipped'); // Valid
 * $result = $orderFilter->withValue('invalid'); // Rejected
 * ```
 * ```
 * // User role validation
 * $roleValue = new EnumValue(
 *     new StringValue(),
 *     'admin', 'moderator', 'editor', 'author', 'subscriber'
 * );
 * $userFilter = new Equals('role', $roleValue);
 * $result = $userFilter->withValue('admin'); // Valid role
 * ```
 * ```
 * // Priority level validation
 * $priorityValue = new EnumValue(
 *     new IntValue(),
 *     1, 2, 3, 4, 5 // 1=urgent, 5=low
 * );
 * $taskFilter = new Equals('priority', $priorityValue);
 * $result = $taskFilter->withValue('3'); // Converts to int 3, valid
 * $result = $taskFilter->withValue(6); // Invalid priority
 * ```
 * ```
 * // Product category validation
 * $categoryValue = new EnumValue(
 *     new StringValue(),
 *     'electronics', 'clothing', 'books', 'sports', 'home'
 * );
 * $productFilter = new Equals('category', $categoryValue);
 * $result = $productFilter->withValue('electronics'); // Valid
 * ```
 * ```
 * // Size validation
 * $sizeValue = new EnumValue(
 *     new StringValue(),
 *     'xs', 's', 'm', 'l', 'xl', 'xxl'
 * );
 * $clothingFilter = new InArray('available_sizes', $sizeValue);
 * $result = $clothingFilter->withValue(['m', 'l', 'xl']); // Valid sizes
 * ```
 * ```
 * // API endpoint validation
 * $sortValue = new EnumValue(
 *     new StringValue(),
 *     'name', 'price', 'date', 'popularity', 'rating'
 * );
 * $apiFilter = new Equals('sort_by', $sortValue);
 * // GET /api/products?sort_by=price
 * $result = $apiFilter->withValue($_GET['sort_by']);
 * ```
 * ```
 * // Configuration validation
 * $logLevelValue = new EnumValue(
 *     new StringValue(),
 *     'debug', 'info', 'warning', 'error', 'critical'
 * );
 * $configFilter = new Equals('log_level', $logLevelValue);
 * $result = $configFilter->withValue('error'); // Valid log level
 * ```
 * ```
 * // Database enum validation
 * $genderValue = new EnumValue(
 *     new StringValue(),
 *     'male', 'female', 'other', 'prefer_not_to_say'
 * );
 * $profileFilter = new Equals('gender', $genderValue);
 * $result = $profileFilter->withValue('other'); // Valid gender option
 * ```
 * ```
 * // Numeric enum validation
 * $ratingValue = new EnumValue(
 *     new IntValue(),
 *     1, 2, 3, 4, 5 // Star ratings
 * );
 * $reviewFilter = new Gte('rating', $ratingValue);
 * $result = $reviewFilter->withValue('4'); // Converts to int 4
 * ```
 * ```
 * // Mixed type enum (not recommended, but possible)
 * $mixedValue = new EnumValue(
 *     new NumericValue(),
 *     1, 2.5, 5, 10.0 // Different numeric types
 * );
 * $result = $mixedValue->withValue('2.5'); // Converts to numeric 2.5
 * ```
 * ```
 * // Validation examples
 * $statusValue = new EnumValue(new StringValue(), 'active', 'inactive', 'pending');
 *
 * // Valid inputs
 * $statusValue->accepts('active');     // true
 * $statusValue->accepts('inactive');   // true
 * $statusValue->accepts('pending');    // true
 *
 * // Invalid inputs
 * $statusValue->accepts('deleted');    // false - not in enum
 * $statusValue->accepts('ACTIVE');     // false - case sensitive
 * $statusValue->accepts(1);            // false - wrong type
 * $statusValue->accepts([]);           // false - array not allowed
 * ```
 * ```
 * // Type conversion examples
 * $numericEnum = new EnumValue(new IntValue(), 1, 2, 3, 4, 5);
 *
 * $numericEnum->accepts('3');          // true - string converts to int
 * $numericEnum->accepts(3);            // true - already int
 * $numericEnum->accepts(3.0);          // true - float converts to int
 * $numericEnum->convert('3');          // Returns int 3
 * $numericEnum->accepts('3.5');        // depends on IntValue conversion rules
 * $numericEnum->accepts(6);            // false - not in enum
 * ```
 */
final class EnumValue implements ValueInterface
{
    private readonly array $values;

    public function __construct(
        private readonly ValueInterface $base,
        mixed ...$values,
    ) {
        if ($base instanceof self) {
            throw new ValueException(\sprintf('Nested value type not allowed, got `%s`', $base::class));
        }

        $this->values = $this->convertEnum(\array_unique($values));
    }

    public function accepts(mixed $value): bool
    {
        if (!$this->base->accepts($value)) {
            return false;
        }

        return \in_array($this->base->convert($value), $this->values, true);
    }

    public function convert(mixed $value): mixed
    {
        return $this->base->convert($value);
    }

    private function convertEnum(array $values): array
    {
        if (empty($values)) {
            throw new ValueException('Enum set should not be empty');
        }

        $type = new ArrayValue($this->base);
        if (!$type->accepts($values)) {
            throw new ValueException(
                \sprintf(
                    '"Got non-compatible values, expected only compatible with `%s`.',
                    $this->base::class,
                ),
            );
        }

        return $type->convert($values);
    }
}
