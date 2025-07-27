<?php

declare(strict_types=1);

namespace Spiral\DataGrid\Specification\Value;

/**
 * Validates values that are strictly less than zero (negative numbers).
 * This is a concrete implementation of CompareValue that only accepts negative values.
 *
 * ```
 * // Account balance deficit tracking
 * $deficitValue = new NegativeValue(new NumericValue());
 * $accountFilter = new Lt('account_balance', $deficitValue);
 * $result = $accountFilter->withValue(-150.50); // Valid deficit
 * $result = $accountFilter->withValue(100);     // Invalid - positive balance
 * ```
 * ```
 * // Below-freezing temperature monitoring
 * $belowFreezing = new NegativeValue(new FloatValue());
 * $weatherFilter = new Lt('temperature_celsius', $belowFreezing);
 * $result = $weatherFilter->withValue(-5.2);  // Valid - below freezing
 * $result = $weatherFilter->withValue(0);     // Invalid - not negative
 * ```
 * ```
 * // Financial loss tracking
 * $lossValue = new NegativeValue(new NumericValue());
 * $financialFilter = new Equals('quarterly_loss', $lossValue);
 * $result = $financialFilter->withValue('-25000'); // Valid loss amount
 * $result = $financialFilter->withValue('15000');  // Invalid - profit, not loss
 * ```
 * ```
 * // Elevation below sea level
 * $belowSeaLevel = new NegativeValue(new IntValue());
 * $elevationFilter = new Lt('elevation_meters', $belowSeaLevel);
 * $result = $elevationFilter->withValue(-85);  // Valid - below sea level
 * $result = $elevationFilter->withValue(100); // Invalid - above sea level
 * ```
 * ```
 * // Gaming penalty system
 * $penaltyValue = new NegativeValue(new IntValue());
 * $gameFilter = new Equals('penalty_points', $penaltyValue);
 * $result = $gameFilter->withValue(-10); // Valid penalty
 * $result = $gameFilter->withValue(5);   // Invalid - reward, not penalty
 * ```
 * ```
 * // Performance decline tracking
 * $declineValue = new NegativeValue(new FloatValue());
 * $performanceFilter = new Lt('growth_rate', $declineValue);
 * $result = $performanceFilter->withValue(-2.5); // Valid - declining 2.5%
 * $result = $performanceFilter->withValue(1.2);  // Invalid - positive growth
 * ```
 * ```
 * // Voltage measurement (negative voltages)
 * $negativeVoltage = new NegativeValue(new FloatValue());
 * $electricalFilter = new Between('voltage', $negativeVoltage);
 * $result = $electricalFilter->withValue([-12.0, -5.0]); // Valid negative range
 * ```
 * ```
 * // Geographic coordinates (Southern/Western hemispheres)
 * $southernLatitude = new NegativeValue(new FloatValue());
 * $locationFilter = new Lt('latitude', $southernLatitude);
 * $result = $locationFilter->withValue(-33.8688); // Sydney, Australia
 * $result = $locationFilter->withValue(40.7128);  // Invalid - Northern hemisphere
 * ```
 * ```
 * // Validation examples
 * $negativeValue = new NegativeValue(new NumericValue());
 *
 * // Valid inputs (negative numbers)
 * $negativeValue->accepts(-10);      // true
 * $negativeValue->accepts('-5.5');   // true
 * $negativeValue->accepts(-0.001);   // true
 * $negativeValue->accepts('-100');   // true
 *
 * // Invalid inputs (zero or positive)
 * $negativeValue->accepts(0);        // false - not negative
 * $negativeValue->accepts(10);       // false - positive
 * $negativeValue->accepts('0');      // false - zero
 * $negativeValue->accepts('5.5');    // false - positive
 * ```
 *
 * Important notes:
 * - Only accepts values that are strictly less than zero (< 0)
 * - Zero is NOT considered negative (returns false)
 * - Uses the base ValueInterface for type conversion before comparison
 * - Useful for scenarios requiring explicit negative value validation
 * - Opposite of PositiveValue (which requires > 0)
 * - Different from NonPositiveValue (which allows <= 0, including zero)
 */
final class NegativeValue extends CompareValue
{
    protected function compare(mixed $value): bool
    {
        return $value < 0;
    }
}
