<?php

declare(strict_types=1);

namespace Spiral\DataGrid\Specification\Value;

use Spiral\DataGrid\Exception\ValueException;
use Spiral\DataGrid\Specification\Value\RangeValue\Boundary;
use Spiral\DataGrid\Specification\ValueInterface;

/**
 * Validates values that fall within a specified range between two boundaries.
 * Supports inclusive/exclusive boundaries and handles boundary validation and ordering.
 *
 * Boundary types:
 * - Inclusive: Value can equal the boundary (>= or <=)
 * - Exclusive: Value cannot equal the boundary (> or <)
 * - Empty: No boundary limit on that side
 *
 * ```
 * // Age range validation (18-65 inclusive)
 * $ageValue = new RangeValue(
 *     new IntValue(),
 *     Boundary::including(18),    // Age >= 18
 *     Boundary::including(65)     // Age <= 65
 * );
 * $ageFilter = new Between('age', $ageValue);
 * $ageValue->accepts(25);    // true - within range
 * $ageValue->accepts(18);    // true - inclusive boundary
 * $ageValue->accepts(65);    // true - inclusive boundary
 * $ageValue->accepts(17);    // false - below minimum
 * $ageValue->accepts(66);    // false - above maximum
 * ```
 * ```
 * // Price range validation (exclusive upper bound)
 * $priceValue = new RangeValue(
 *     new NumericValue(),
 *     Boundary::including(10.0),  // Price >= $10.00
 *     Boundary::excluding(100.0)  // Price < $100.00 (not including)
 * );
 * $priceFilter = new Between('price', $priceValue);
 * $priceValue->accepts(10.0);    // true - inclusive minimum
 * $priceValue->accepts(99.99);   // true - below exclusive maximum
 * $priceValue->accepts(100.0);   // false - exclusive maximum
 * ```
 * ```
 * // Temperature sensor range (both exclusive)
 * $tempValue = new RangeValue(
 *     new FloatValue(),
 *     Boundary::excluding(-40.0), // Temperature > -40°C
 *     Boundary::excluding(85.0)   // Temperature < 85°C
 * );
 * $sensorFilter = new Between('temperature', $tempValue);
 * $tempValue->accepts(-39.9);    // true - above minimum
 * $tempValue->accepts(84.9);     // true - below maximum
 * $tempValue->accepts(-40.0);    // false - exclusive minimum
 * $tempValue->accepts(85.0);     // false - exclusive maximum
 * ```
 * ```
 * // One-sided range (minimum only)
 * $minimumValue = new RangeValue(
 *     new IntValue(),
 *     Boundary::including(1),     // Value >= 1
 *     Boundary::empty()           // No maximum limit
 * );
 * $positiveFilter = new Gte('quantity', $minimumValue);
 * $minimumValue->accepts(1);      // true - meets minimum
 * $minimumValue->accepts(1000);   // true - no maximum limit
 * $minimumValue->accepts(0);      // false - below minimum
 * ```
 * ```
 * // One-sided range (maximum only)
 * $maximumValue = new RangeValue(
 *     new FloatValue(),
 *     Boundary::empty(),          // No minimum limit
 *     Boundary::including(100.0)  // Value <= 100.0
 * );
 * $limitFilter = new Lte('percentage', $maximumValue);
 * $maximumValue->accepts(50.0);   // true - below maximum
 * $maximumValue->accepts(100.0);  // true - at inclusive maximum
 * $maximumValue->accepts(101.0);  // false - above maximum
 * ```
 * ```
 * // Validation examples
 * $rangeValue = new RangeValue(
 *     new IntValue(),
 *     Boundary::including(10),
 *     Boundary::excluding(20)
 * );
 *
 * // Valid inputs (10 <= value < 20)
 * $rangeValue->accepts(10);      // true - inclusive minimum
 * $rangeValue->accepts(15);      // true - within range
 * $rangeValue->accepts(19);      // true - below exclusive maximum
 *
 * // Invalid inputs
 * $rangeValue->accepts(9);       // false - below minimum
 * $rangeValue->accepts(20);      // false - at exclusive maximum
 * $rangeValue->accepts(25);      // false - above maximum
 * ```
 * ```
 * // Conversion examples
 * $rangeValue = new RangeValue(new NumericValue(), Boundary::including(1), Boundary::including(100));
 *
 * $rangeValue->convert(50);      // Returns 50 (within range)
 * $rangeValue->convert('75');    // Returns 75 (converted and within range)
 * $rangeValue->convert(1.0);     // Returns 1 (at boundary, within range)
 * ```
 * ```
 * // Grade validation (0-100 inclusive)
 * $gradeValue = new RangeValue(
 *     new FloatValue(),
 *     Boundary::including(0.0),
 *     Boundary::including(100.0)
 * );
 * $gradeFilter = new Between('test_score', $gradeValue);
 * $gradeValue->accepts(85.5);    // true - valid grade
 * $gradeValue->accepts(0.0);     // true - minimum grade
 * $gradeValue->accepts(100.0);   // true - maximum grade
 * $gradeValue->accepts(-5.0);    // false - below minimum
 * $gradeValue->accepts(105.0);   // false - above maximum
 * ```
 *
 * Important notes:
 * - Boundaries are automatically ordered (smaller becomes minimum)
 * - Same boundary values throw ValueException during construction
 * - Empty boundaries (null) remove that limit
 * - Base ValueInterface handles type conversion before range checking
 * - Inclusive boundaries use >= and <= comparisons
 * - Exclusive boundaries use > and < comparisons
 * - Both boundaries must be compatible with the base value type
 * - Useful for constraining inputs to acceptable operational ranges
 */
final class RangeValue implements ValueInterface
{
    private Boundary $from;
    private Boundary $to;

    public function __construct(
        private readonly ValueInterface $base,
        ?RangeValue\Boundary $from = null,
        ?RangeValue\Boundary $to = null,
    ) {
        $from ??= RangeValue\Boundary::empty();
        $to ??= RangeValue\Boundary::empty();

        $this->validateBoundaries($from, $to);
        $this->setBoundaries($from, $to);
    }

    public function accepts(mixed $value): bool
    {
        return $this->base->accepts($value) && $this->acceptsFrom($value) && $this->acceptsTo($value);
    }

    public function convert(mixed $value): mixed
    {
        return $this->base->convert($value);
    }

    private function validateBoundaries(RangeValue\Boundary $from, RangeValue\Boundary $to): void
    {
        if (!$this->acceptsBoundary($from) || !$this->acceptsBoundary($to)) {
            throw new ValueException('Range boundaries should be applicable via passed type.');
        }

        if ($this->convertBoundaryValue($from) === $this->convertBoundaryValue($to)) {
            throw new ValueException('Range boundaries should be different.');
        }
    }

    private function acceptsBoundary(RangeValue\Boundary $boundary): bool
    {
        return $boundary->empty || $this->base->accepts($boundary->value);
    }

    private function convertBoundaryValue(RangeValue\Boundary $boundary)
    {
        return $boundary->empty ? null : $this->base->convert($boundary->value);
    }

    private function acceptsFrom(mixed $value): bool
    {
        if ($this->from->empty) {
            return true;
        }

        $from = $this->base->convert($this->from->value);

        return $this->from->include ? ($value >= $from) : ($value > $from);
    }

    private function acceptsTo(mixed $value): bool
    {
        if ($this->to->empty) {
            return true;
        }

        $to = $this->base->convert($this->to->value);

        return $this->to->include ? ($value <= $to) : ($value < $to);
    }

    private function setBoundaries(RangeValue\Boundary $from, RangeValue\Boundary $to): void
    {
        //Swap if from < to and both not empty
        if (!$from->empty && !$to->empty && $from->value > $to->value) {
            [$from, $to] = [$to, $from];
        }

        $this->from = $from;
        $this->to = $to;
    }
}
