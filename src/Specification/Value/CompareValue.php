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
 * Real-world usage examples:
 * - Financial validation: Ensure amounts are positive, prevent negative balances
 * - Inventory management: Stock quantities must be non-negative
 * - Rating systems: Ratings must be positive (1-5 stars, not negative)
 * - Age validation: Age must be non-negative (can't be negative years old)
 * - Performance metrics: Response times, throughput must be positive
 * - Mathematical calculations: Prevent division by zero, ensure valid inputs
 * - Coordinate systems: Validate geographic coordinates within expected ranges
 * - Score systems: Game scores, test scores with specific sign requirements
 *
 * Available concrete implementations:
 * - PositiveValue: > 0 (strictly greater than zero)
 * - NegativeValue: < 0 (strictly less than zero)
 * - NonNegativeValue: >= 0 (zero or positive)
 * - NonPositiveValue: <= 0 (zero or negative)
 *
 * @example
 * // Custom comparison value implementation
 * class CustomCompareValue extends CompareValue {
 *     protected function compare(mixed $value): bool {
 *         return $value % 2 === 0; // Only even numbers
 *     }
 * }
 *
 * @example
 * // Price validation (must be positive)
 * $priceValue = new PositiveValue(new NumericValue());
 * $priceFilter = new Gte('price', $priceValue);
 * $result = $priceFilter->withValue(25.50); // Valid positive price
 * $result = $priceFilter->withValue(-10); // Invalid - rejected
 *
 * @example
 * // Stock quantity validation (non-negative)
 * $stockValue = new NonNegativeValue(new IntValue());
 * $stockFilter = new Gte('quantity', $stockValue);
 * $result = $stockFilter->withValue(0); // Valid - out of stock but not negative
 * $result = $stockFilter->withValue(100); // Valid - in stock
 * $result = $stockFilter->withValue(-5); // Invalid - negative stock not allowed
 *
 * @example
 * // Temperature readings (can be negative)
 * $temperatureValue = new AnyValue(); // No comparison restriction
 * $tempFilter = new Between('temperature', $temperatureValue);
 * // vs restricting to positive only:
 * $positiveTempValue = new PositiveValue(new NumericValue());
 * $positiveTempFilter = new Between('kelvin_temperature', $positiveTempValue);
 *
 * @example
 * // Age validation (non-negative)
 * $ageValue = new NonNegativeValue(new IntValue());
 * $userFilter = new Gte('age', $ageValue);
 * $result = $userFilter->withValue(25); // Valid age
 * $result = $userFilter->withValue(0); // Valid (newborn)
 * $result = $userFilter->withValue(-5); // Invalid - negative age
 *
 * @example
 * // Debt amount (can be negative for credits)
 * $debtValue = new AnyValue(); // Allow negative (credit) and positive (debt)
 * // vs account balance that shouldn't go below zero:
 * $balanceValue = new NonNegativeValue(new NumericValue());
 * $balanceFilter = new Gte('account_balance', $balanceValue);
 *
 * @example
 * // Performance metrics validation
 * $responseTimeValue = new PositiveValue(new NumericValue());
 * $performanceFilter = new Lt('response_time_ms', $responseTimeValue);
 * $result = $performanceFilter->withValue(150.5); // Valid response time
 * $result = $performanceFilter->withValue(0); // Invalid - no response time
 * $result = $performanceFilter->withValue(-10); // Invalid - negative time
 *
 * @example
 * // Score validation (game scores can be negative, test scores typically positive)
 * $gameScoreValue = new AnyValue(); // Allow negative scores (penalties)
 * $testScoreValue = new NonNegativeValue(new NumericValue()); // Test scores >= 0
 *
 * $gameFilter = new Gte('game_score', $gameScoreValue);
 * $testFilter = new Gte('test_score', $testScoreValue);
 *
 * @example
 * // Usage with different base value types
 * $positiveInt = new PositiveValue(new IntValue());
 * $positiveFloat = new PositiveValue(new FloatValue());
 * $positiveNumeric = new PositiveValue(new NumericValue());
 *
 * $positiveInt->accepts(5);      // true
 * $positiveInt->accepts(0);      // false (not positive)
 * $positiveInt->accepts(-3);     // false (negative)
 * $positiveFloat->accepts(0.1);  // true
 * $positiveFloat->accepts(0.0);  // false (not positive)
 *
 * Important notes:
 * - Always uses a base ValueInterface for type conversion before comparison
 * - Prevents nesting of CompareValue types (throws ValueException)
 * - Comparison happens after value conversion by the base type
 * - ArrayValue cannot be used as base type (throws ValueException)
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
