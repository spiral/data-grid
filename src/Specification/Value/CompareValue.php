<?php

declare(strict_types=1);

namespace Spiral\DataGrid\Specification\Value;

use Spiral\DataGrid\Exception\ValueException;
use Spiral\DataGrid\Specification\ValueInterface;

/**
 * Abstract base class for value types that compare values against zero.
 * Provides common functionality for positive/negative/non-positive/non-negative value validation.
 * This is used by PositiveValue, NegativeValue, NonPositiveValue, and NonNegativeValue.
 *
 * ```
 * // Custom comparison value implementation
 * class CustomCompareValue extends CompareValue {
 *     protected function compare(mixed $value): bool {
 *         return $value % 2 === 0; // Only even numbers
 *     }
 * }
 * ```
 */
abstract class CompareValue implements ValueInterface
{
    private readonly ValueInterface $base;

    public function __construct(ValueInterface $base)
    {
        if ($base instanceof ArrayValue) {
            throw new ValueException(\sprintf('Scalar value type expected, got `%s`', $base::class));
        }

        $this->base = $base instanceof self ? $base->base : $base;
    }

    public function accepts(mixed $value): bool
    {
        if (!$this->base->accepts($value)) {
            return false;
        }

        return $this->compare($this->convert($value));
    }

    public function convert(mixed $value): mixed
    {
        return $this->base->convert($value);
    }

    /**
     * Checks if value comparison with zero is ok.
     */
    abstract protected function compare(mixed $value): bool;
}
