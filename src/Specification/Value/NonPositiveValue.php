<?php

declare(strict_types=1);

namespace Spiral\DataGrid\Specification\Value;

/**
 * Validates values that are less than or equal to zero (non-positive numbers).
 * This is a concrete implementation of CompareValue that accepts zero and negative values.
 *
 * Use cases:
 * - Temperature alerts: Monitor freezing and sub-zero conditions
 * - Financial oversight: Track zero balances and debts
 * - Quality assurance: Identify zero and declining performance
 * - Geographic mapping: Handle coordinates at and below reference points
 * - System monitoring: Track neutral and negative states
 *
 * ```
 * // Temperature monitoring (freezing and below)
 * $freezingValue = new NonPositiveValue(new FloatValue());
 * $weatherFilter = new Lte('temperature_celsius', $freezingValue);
 * $result = $freezingValue->withValue(0.0);   // Valid - freezing point
 * $result = $freezingValue->withValue(-5.2);  // Valid - below freezing
 * $result = $freezingValue->withValue(10.0);  // Invalid - above freezing
 * ```
 * ```
 * // Account balance monitoring (zero and overdraft)
 * $balanceValue = new NonPositiveValue(new NumericValue());
 * $accountFilter = new Lte('account_balance', $balanceValue);
 * $result = $balanceValue->withValue(0.00);     // Valid - zero balance
 * $result = $balanceValue->withValue(-150.50);  // Valid - overdraft
 * $result = $balanceValue->withValue(100.00);   // Invalid - positive balance
 * ```
 * ```
 * // Elevation monitoring (sea level and below)
 * $elevationValue = new NonPositiveValue(new IntValue());
 * $locationFilter = new Lte('elevation_meters', $elevationValue);
 * $result = $elevationValue->withValue(0);    // Valid - sea level
 * $result = $elevationValue->withValue(-85);  // Valid - below sea level (Dead Sea)
 * $result = $elevationValue->withValue(100);  // Invalid - above sea level
 * ```
 * ```
 * // Performance penalty tracking
 * $penaltyValue = new NonPositiveValue(new IntValue());
 * $gameFilter = new Lte('score_adjustment', $penaltyValue);
 * $result = $penaltyValue->withValue(0);   // Valid - no adjustment
 * $result = $penaltyValue->withValue(-10); // Valid - penalty points
 * $result = $penaltyValue->withValue(5);   // Invalid - bonus points (positive)
 * ```
 * ```
 * // Quality decline monitoring
 * $qualityValue = new NonPositiveValue(new FloatValue());
 * $qualityFilter = new Lte('quality_change', $qualityValue);
 * $result = $qualityValue->withValue(0.0);  // Valid - no change
 * $result = $qualityValue->withValue(-2.5); // Valid - quality declined 2.5%
 * $result = $qualityValue->withValue(1.8);  // Invalid - quality improved
 * ```
 * ```
 * // Voltage measurement (ground and negative)
 * $voltageValue = new NonPositiveValue(new FloatValue());
 * $electricalFilter = new Lte('voltage', $voltageValue);
 * $result = $voltageValue->withValue(0.0);   // Valid - ground voltage
 * $result = $voltageValue->withValue(-12.0); // Valid - negative voltage
 * $result = $voltageValue->withValue(5.0);   // Invalid - positive voltage
 * ```
 * ```
 * // Financial loss and break-even tracking
 * $profitValue = new NonPositiveValue(new NumericValue());
 * $financialFilter = new Lte('quarterly_profit', $profitValue);
 * $result = $profitValue->withValue(0);       // Valid - break even
 * $result = $profitValue->withValue(-25000);  // Valid - loss
 * $result = $profitValue->withValue(15000);   // Invalid - profit
 * ```
 * ```
 * // Geographic coordinate validation (Western/Southern hemispheres and reference lines)
 * $longitudeValue = new NonPositiveValue(new FloatValue());
 * $locationFilter = new Lte('longitude', $longitudeValue);
 * $result = $longitudeValue->withValue(0.0);     // Valid - Prime Meridian
 * $result = $longitudeValue->withValue(-74.006); // Valid - New York (Western hemisphere)
 * $result = $longitudeValue->withValue(2.3522);  // Invalid - Eastern hemisphere
 * ```
 * ```
 * // Validation examples
 * $nonPositiveValue = new NonPositiveValue(new NumericValue());
 *
 * // Valid inputs (zero or negative)
 * $nonPositiveValue->accepts(0);        // true - zero allowed
 * $nonPositiveValue->accepts(-10);      // true - negative allowed
 * $nonPositiveValue->accepts('0');      // true - zero string
 * $nonPositiveValue->accepts('-5.5');   // true - negative string
 * $nonPositiveValue->accepts(0.0);      // true - zero float
 * $nonPositiveValue->accepts(-100.5);   // true - negative float
 *
 * // Invalid inputs (positive)
 * $nonPositiveValue->accepts(1);        // false - positive not allowed
 * $nonPositiveValue->accepts('5.5');    // false - positive string
 * $nonPositiveValue->accepts(0.001);    // false - small positive
 * ```
 *
 * Important notes:
 * - Accepts values that are less than or equal to zero (<= 0)
 * - Zero is explicitly allowed and considered valid
 * - Uses the base ValueInterface for type conversion before comparison
 * - Useful for scenarios where zero represents a neutral/reference state
 * - Different from NegativeValue (which excludes zero, requires < 0)
 * - Opposite of NonNegativeValue (which requires >= 0)
 * - More permissive than NegativeValue for cases where zero is meaningful
 */
final class NonPositiveValue extends CompareValue
{
    protected function compare(mixed $value): bool
    {
        return $value <= 0;
    }
}
