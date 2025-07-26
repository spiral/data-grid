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
 * Real-world usage examples:
 * - Age restrictions: Valid ages between 18-65 for job applications
 * - Price ranges: Products between $50-$500 price range
 * - Date ranges: Events between specific start and end dates
 * - Performance limits: Response times between acceptable thresholds
 * - Measurement validation: Temperature readings within sensor range
 * - Score validation: Test scores between minimum and maximum values
 * - Geographic boundaries: Coordinates within specific regions
 * - Configuration limits: Settings within acceptable operational ranges
 *
 * Boundary types:
 * - Inclusive: Value can equal the boundary (>= or <=)
 * - Exclusive: Value cannot equal the boundary (> or <)
 * - Empty: No boundary limit on that side
 *
 * @example
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
 *
 * @example
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
 *
 * @example
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
 *
 * @example
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
 *
 * @example
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
 *
 * @example
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
 *
 * @example
 * // Conversion examples
 * $rangeValue = new RangeValue(new NumericValue(), Boundary::including(1), Boundary::including(100));
 *
 * $rangeValue->convert(50);      // Returns 50 (within range)
 * $rangeValue->convert('75');    // Returns 75 (converted and within range)
 * $rangeValue->convert(1.0);     // Returns 1 (at boundary, within range)
 *
 * @example
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
 *
 * @example
 * // Working hours validation (8-12 hours inclusive)
 * $hoursValue = new RangeValue(
 *     new FloatValue(),
 *     Boundary::including(8.0),
 *     Boundary::including(12.0)
 * );
 * $workFilter = new Between('hours_worked', $hoursValue);
 * $hoursValue->accepts(8.0);     // true - minimum hours
 * $hoursValue->accepts(10.5);    // true - within range
 * $hoursValue->accepts(12.0);    // true - maximum hours
 * $hoursValue->accepts(7.5);     // false - under minimum
 * $hoursValue->accepts(13.0);    // false - over maximum
 *
 * @example
 * // API parameter validation
 * $apiRangeValue = new RangeValue(
 *     new IntValue(),
 *     Boundary::including(1),
 *     Boundary::including(100)
 * );
 * if ($apiRangeValue->accepts($_GET['limit'])) {
 *     $validLimit = $apiRangeValue->convert($_GET['limit']);
 *     // Use validated limit within acceptable range
 * }
 *
 * @example
 * // Date range validation (this year only)
 * $dateRangeValue = new RangeValue(
 *     new DatetimeValue(),
 *     Boundary::including('2024-01-01'),
 *     Boundary::excluding('2025-01-01')  // Before next year
 * );
 * $dateFilter = new Between('event_date', $dateRangeValue);
 *
 * @example
 * // Performance metrics (response time 0-5000ms)
 * $responseValue = new RangeValue(
 *     new FloatValue(),
 *     Boundary::excluding(0.0),    // Response time > 0
 *     Boundary::including(5000.0)  // Response time <= 5000ms
 * );
 * $performanceFilter = new Lt('response_time', $responseValue);
 *
 * @example
 * // Complex validation with business rules
 * $businessRangeValue = new RangeValue(
 *     new NumericValue(),
 *     Boundary::including(1000),   // Minimum order $1000
 *     Boundary::excluding(50000)   // Under $50000 (different processing)
 * );
 * $orderFilter = new Between('order_total', $businessRangeValue);
 *
 * @example
 * // Error handling and boundary validation
 * try {
 *     // This will throw ValueException - boundaries must be different
 *     $invalidRange = new RangeValue(
 *         new IntValue(),
 *         Boundary::including(10),
 *         Boundary::including(10)  // Same as minimum - invalid
 *     );
 * } catch (ValueException $e) {
 *     // Handle boundary validation error
 * }
 *
 * @example
 * // Automatic boundary ordering
 * $autoOrderedRange = new RangeValue(
 *     new IntValue(),
 *     Boundary::including(50),     // Will become the maximum
 *     Boundary::including(10)      // Will become the minimum (auto-swapped)
 * );
 * // Results in range: 10 <= value <= 50
 *
 * @example
 * // Geographic coordinate validation
 * $latitudeValue = new RangeValue(
 *     new FloatValue(),
 *     Boundary::including(-90.0),  // South pole
 *     Boundary::including(90.0)    // North pole
 * );
 * $locationFilter = new Between('latitude', $latitudeValue);
 *
 * @example
 * // Percentage validation (0-100%)
 * $percentageValue = new RangeValue(
 *     new FloatValue(),
 *     Boundary::including(0.0),
 *     Boundary::including(100.0)
 * );
 * $percentFilter = new Between('completion_percentage', $percentageValue);
 *
 * @example
 * // Form validation
 * $ageRangeValue = new RangeValue(new IntValue(), Boundary::including(18), Boundary::including(120));
 * if ($ageRangeValue->accepts($_POST['age'])) {
 *     $validAge = $ageRangeValue->convert($_POST['age']);
 *     // Process age within acceptable range
 * }
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
