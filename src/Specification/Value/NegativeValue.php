<?php

declare(strict_types=1);

namespace Spiral\DataGrid\Specification\Value;

/**
 * Validates values that are strictly less than zero (negative numbers).
 * This is a concrete implementation of CompareValue that only accepts negative values.
 *
 * Real-world usage examples:
 * - Financial systems: Account deficits, debt amounts, negative balances
 * - Temperature monitoring: Below-freezing temperatures, cooling systems
 * - Performance metrics: Negative growth rates, declining performance
 * - Geographic data: Below sea level elevations, coordinates in southern/western hemispheres
 * - Scientific measurements: Negative voltages, pressure differences
 * - Gaming systems: Penalty scores, negative point adjustments
 * - Statistical analysis: Negative correlations, decreases, losses
 * - Business metrics: Loss amounts, negative profit margins
 *
 * Use cases:
 * - Debt tracking: Only accept negative amounts for debt entries
 * - Temperature alerts: Monitor sub-zero temperatures
 * - Financial losses: Track losses and deficits
 * - Coordinate systems: Validate negative coordinates
 * - Quality control: Identify negative performance indicators
 *
 * @example
 * // Account balance deficit tracking
 * $deficitValue = new NegativeValue(new NumericValue());
 * $accountFilter = new Lt('account_balance', $deficitValue);
 * $result = $accountFilter->withValue(-150.50); // Valid deficit
 * $result = $accountFilter->withValue(100);     // Invalid - positive balance
 *
 * @example
 * // Below-freezing temperature monitoring
 * $belowFreezing = new NegativeValue(new FloatValue());
 * $weatherFilter = new Lt('temperature_celsius', $belowFreezing);
 * $result = $weatherFilter->withValue(-5.2);  // Valid - below freezing
 * $result = $weatherFilter->withValue(0);     // Invalid - not negative
 *
 * @example
 * // Financial loss tracking
 * $lossValue = new NegativeValue(new NumericValue());
 * $financialFilter = new Equals('quarterly_loss', $lossValue);
 * $result = $financialFilter->withValue('-25000'); // Valid loss amount
 * $result = $financialFilter->withValue('15000');  // Invalid - profit, not loss
 *
 * @example
 * // Elevation below sea level
 * $belowSeaLevel = new NegativeValue(new IntValue());
 * $elevationFilter = new Lt('elevation_meters', $belowSeaLevel);
 * $result = $elevationFilter->withValue(-85);  // Valid - below sea level
 * $result = $elevationFilter->withValue(100); // Invalid - above sea level
 *
 * @example
 * // Gaming penalty system
 * $penaltyValue = new NegativeValue(new IntValue());
 * $gameFilter = new Equals('penalty_points', $penaltyValue);
 * $result = $gameFilter->withValue(-10); // Valid penalty
 * $result = $gameFilter->withValue(5);   // Invalid - reward, not penalty
 *
 * @example
 * // Performance decline tracking
 * $declineValue = new NegativeValue(new FloatValue());
 * $performanceFilter = new Lt('growth_rate', $declineValue);
 * $result = $performanceFilter->withValue(-2.5); // Valid - declining 2.5%
 * $result = $performanceFilter->withValue(1.2);  // Invalid - positive growth
 *
 * @example
 * // Voltage measurement (negative voltages)
 * $negativeVoltage = new NegativeValue(new FloatValue());
 * $electricalFilter = new Between('voltage', $negativeVoltage);
 * $result = $electricalFilter->withValue([-12.0, -5.0]); // Valid negative range
 *
 * @example
 * // Geographic coordinates (Southern/Western hemispheres)
 * $southernLatitude = new NegativeValue(new FloatValue());
 * $locationFilter = new Lt('latitude', $southernLatitude);
 * $result = $locationFilter->withValue(-33.8688); // Sydney, Australia
 * $result = $locationFilter->withValue(40.7128);  // Invalid - Northern hemisphere
 *
 * @example
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
 *
 * @example
 * // Conversion examples
 * $negativeValue = new NegativeValue(new NumericValue());
 *
 * $negativeValue->convert('-10');    // Returns -10
 * $negativeValue->convert(-5.5);     // Returns -5.5
 * $negativeValue->convert('-0.1');   // Returns -0.1
 *
 * @example
 * // Debt management system
 * $debtAmount = new NegativeValue(new NumericValue());
 * $debtFilter = new All(
 *     new Lt('account_balance', $debtAmount),      // Balance is negative
 *     new Gte('credit_limit', new PositiveValue(new NumericValue())) // Credit limit is positive
 * );
 * $result = $debtFilter->withValue(-500.00); // $500 debt
 *
 * @example
 * // Scientific measurement validation
 * $pressureDrop = new NegativeValue(new FloatValue());
 * $pressureFilter = new Lt('pressure_change', $pressureDrop);
 * $result = $pressureFilter->withValue(-15.7); // Pressure decreased by 15.7 units
 *
 * @example
 * // Investment loss tracking
 * $investmentLoss = new NegativeValue(new NumericValue());
 * $portfolioFilter = new Equals('monthly_return', $investmentLoss);
 * $result = $portfolioFilter->withValue('-3.2'); // 3.2% loss this month
 *
 * @example
 * // Quality control - defect tracking
 * $qualityDecline = new NegativeValue(new FloatValue());
 * $qualityFilter = new Lt('quality_change', $qualityDecline);
 * $result = $qualityFilter->withValue(-0.5); // Quality decreased by 0.5%
 *
 * @example
 * // Weather monitoring system
 * $subZeroTemp = new NegativeValue(new FloatValue());
 * $weatherAlert = new Lt('min_temperature', $subZeroTemp);
 * $result = $weatherAlert->withValue(-15.3); // Freeze warning at -15.3°C
 *
 * @example
 * // API validation for negative values
 * $negativeApiValue = new NegativeValue(new NumericValue());
 * if ($negativeApiValue->accepts($_POST['deficit_amount'])) {
 *     $validDeficit = $negativeApiValue->convert($_POST['deficit_amount']);
 *     // Process valid negative amount
 * } else {
 *     // Handle invalid input - not negative
 * }
 *
 * @example
 * // Complex validation with enum
 * $validLossLevels = new EnumValue(
 *     new NegativeValue(new IntValue()),
 *     -1, -2, -3, -5, -10 // Predefined loss levels
 * );
 * $lossFilter = new Equals('loss_level', $validLossLevels);
 * $result = $lossFilter->withValue('-3'); // Valid loss level
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
