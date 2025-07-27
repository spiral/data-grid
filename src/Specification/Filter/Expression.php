<?php

declare(strict_types=1);

namespace Spiral\DataGrid\Specification\Filter;

use Spiral\DataGrid\Specification\FilterInterface;
use Spiral\DataGrid\Specification\ValueInterface;
use Spiral\DataGrid\SpecificationInterface;

/**
 * Abstract base class for expression-based filters.
 *
 * Provides common functionality for filters that operate on a single field expression
 * with a value. This is the foundation for most basic comparison filters.
 *
 * ```
 * // Custom expression filter
 * class CustomFilter extends Expression {
 *     // Implementation specific to your needs
 * }
 * ```
 * ```
 * // Using with dynamic values
 * $filter = new SomeExpressionFilter('field_name', new StringValue());
 * $result = $filter->withValue('user_input');
 * ```
 * ```
 * // Using with fixed values
 * $filter = new SomeExpressionFilter('status', 'active');
 * ```
 */
abstract class Expression implements FilterInterface
{
    /**
     * @param string $expression The field name or expression to filter on
     * @param mixed $value Either a fixed value or ValueInterface for dynamic input validation
     */
    public function __construct(
        protected string $expression,
        protected mixed $value,
    ) {}

    public function withValue(mixed $value): ?SpecificationInterface
    {
        $filter = clone $this;
        if (!$filter->value instanceof ValueInterface) {
            // constant value
            return $filter;
        }

        if (!$filter->value->accepts($value)) {
            // invalid value
            return null;
        }

        // create static filtered value
        $filter->value = $filter->value->convert($value);

        return $filter;
    }

    public function getExpression(): string
    {
        return $this->expression;
    }

    public function getValue(): mixed
    {
        return $this->value;
    }
}
