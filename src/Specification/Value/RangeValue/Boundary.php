<?php

declare(strict_types=1);

namespace Spiral\DataGrid\Specification\Value\RangeValue;

use Spiral\DataGrid\Specification\Value\RangeValue;

/**
 * Represents a boundary in a range value, which can either be inclusive or exclusive.
 * It can also represent an empty boundary.
 *
 * A boundary defines one side of a range filter (either the lower or upper bound).
 * For example, in a price range filter from $10 to $100:
 * - Lower boundary: $10 (inclusive or exclusive)
 * - Upper boundary: $100 (inclusive or exclusive)
 *
 * Examples:
 * - `Boundary::including(10)` creates ">=10" or "<=10" depending on context
 * - `Boundary::excluding(10)` creates ">10" or "<10" depending on context
 * - `Boundary::empty()` represents no limit on that side of the range
 *
 * @see RangeValue For the complete range implementation
 */
class Boundary
{
    private function __construct(
        public mixed $value,
        public bool $include,
        public bool $empty,
    ) {}

    /**
     * Creates a boundary that represents an empty value.
     *
     * An empty boundary means there's no limit on this side of the range.
     * For example, in a "price greater than $10" filter, the upper boundary would be empty.
     *
     * Usage examples:
     * - Price filter with only minimum: lower=Boundary::including(10), upper=Boundary::empty()
     * - Date filter with only "before": lower=Boundary::empty(), upper=Boundary::excluding('2023-12-31')
     */
    public static function empty(): self
    {
        return self::create(null, true);
    }

    /**
     * Creates an inclusive boundary with the given value.
     *
     * An inclusive boundary includes the boundary value itself in the range.
     * - For lower bounds: value >= boundary_value
     * - For upper bounds: value <= boundary_value
     *
     * Usage examples:
     * - `Boundary::including(100)` for "price up to and including $100"
     * - `Boundary::including('2023-01-01')` for "date from January 1st, 2023 onwards"
     */
    public static function including(mixed $value): self
    {
        return self::create($value, true);
    }

    /**
     * Creates an exclusive boundary with the given value.
     *
     * An exclusive boundary excludes the boundary value itself from the range.
     * - For lower bounds: value > boundary_value
     * - For upper bounds: value < boundary_value
     *
     * Usage examples:
     * - `Boundary::excluding(0)` for "price greater than $0" (excludes free items)
     * - `Boundary::excluding('2023-12-31')` for "date before December 31st, 2023"
     */
    public static function excluding(mixed $value): self
    {
        return self::create($value, false);
    }

    private static function create(mixed $value, bool $include): self
    {
        return new self($value, $include, $value === null);
    }
}
