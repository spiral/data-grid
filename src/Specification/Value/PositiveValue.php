<?php

declare(strict_types=1);

namespace Spiral\DataGrid\Specification\Value;

/**
 * Validates values that are strictly greater than zero (positive numbers).
 * This is a concrete implementation of CompareValue that only accepts positive values.
 *
 * Real-world usage examples:
 * - Price validation: Product prices must be positive (> 0)
 * - Quantity validation: Order quantities must be positive
 * - ID validation: Database IDs are typically positive integers
 * - Performance metrics: Response times, throughput must be positive
 * - Age validation: Age must be positive (can't be 0 or negative)
 * - Rating systems: Star ratings must be positive (1-5 stars)
 * - Financial amounts: Transaction amounts must be positive
 * - Measurement validation: Physical measurements (weight, height) must be positive
 *
 * Use cases:
 * - E-commerce: Ensure product prices are valid
 * - Inventory: Validate stock quantities are meaningful
 * - User data: Ensure age, height, weight are realistic
 * - Financial: Validate transaction amounts are positive
 * - Performance: Ensure metrics are valid positive values
 *
 * @example
 * // Product price validation
 * $priceValue = new PositiveValue(new NumericValue());
 * $productFilter = new Gte('price', $priceValue);
 * $result = $productFilter->withValue(19.99); // Valid - positive price
 * $result = $productFilter->withValue(0);     // Invalid - zero price
 * $result = $productFilter->withValue(-5);    // Invalid - negative price
 *
 * @example
 * // Order quantity validation
 * $quantityValue = new PositiveValue(new IntValue());
 * $orderFilter = new Gte('quantity', $quantityValue);
 * $result = $orderFilter->withValue(3);  // Valid - ordering 3 items
 * $result = $orderFilter->withValue(0);  // Invalid - zero quantity
 * $result = $orderFilter->withValue(-1); // Invalid - negative quantity
 *
 * @example
 * // User age validation (must be positive)
 * $ageValue = new PositiveValue(new IntValue());
 * $userFilter = new Gte('age', $ageValue);
 * $result = $ageValue->withValue(25); // Valid - 25 years old
 * $result = $ageValue->withValue(0);  // Invalid - zero age not meaningful
 * $result = $ageValue->withValue(-5); // Invalid - negative age impossible
 *
 * @example
 * // Database ID validation
 * $idValue = new PositiveValue(new IntValue());
 * $recordFilter = new Equals('user_id', $idValue);
 * $result = $recordFilter->withValue(123); // Valid - positive ID
 * $result = $recordFilter->withValue(0);   // Invalid - zero ID typically not used
 * $result = $recordFilter->withValue(-1);  // Invalid - negative ID not valid
 *
 * @example
 * // Performance metrics validation
 * $responseTimeValue = new PositiveValue(new FloatValue());
 * $performanceFilter = new Lt('response_time_ms', $responseTimeValue);
 * $result = $performanceFilter->withValue(150.5); // Valid - positive response time
 * $result = $performanceFilter->withValue(0.0);   // Invalid - zero response time
 * $result = $performanceFilter->withValue(-10.0); // Invalid - negative time impossible
 *
 * @example
 * // Star rating validation (1-5 stars, must be positive)
 * $ratingValue = new PositiveValue(new IntValue());
 * $reviewFilter = new Between('rating', $ratingValue);
 * $result = $reviewFilter->withValue([1, 5]); // Valid - 1 to 5 stars
 * $result = $reviewFilter->withValue([0, 5]); // Invalid - 0 stars not positive
 *
 * @example
 * // Weight validation
 * $weightValue = new PositiveValue(new FloatValue());
 * $shippingFilter = new Lt('weight_kg', $weightValue);
 * $result = $shippingFilter->withValue(2.5);  // Valid - 2.5kg
 * $result = $shippingFilter->withValue(0.0);  // Invalid - zero weight
 * $result = $shippingFilter->withValue(-1.0); // Invalid - negative weight
 *
 * @example
 * // Financial transaction amount
 * $amountValue = new PositiveValue(new NumericValue());
 * $transactionFilter = new Gte('amount', $amountValue);
 * $result = $transactionFilter->withValue(100.50); // Valid - positive amount
 * $result = $transactionFilter->withValue(0);      // Invalid - zero transaction
 * $result = $transactionFilter->withValue(-25);    // Invalid - negative amount
 *
 * @example
 * // Validation examples
 * $positiveValue = new PositiveValue(new NumericValue());
 *
 * // Valid inputs (positive numbers)
 * $positiveValue->accepts(10);       // true
 * $positiveValue->accepts(0.1);      // true
 * $positiveValue->accepts('5.5');    // true
 * $positiveValue->accepts('100');    // true
 * $positiveValue->accepts(0.001);    // true
 *
 * // Invalid inputs (zero or negative)
 * $positiveValue->accepts(0);        // false - not positive
 * $positiveValue->accepts(-10);      // false - negative
 * $positiveValue->accepts('0');      // false - zero string
 * $positiveValue->accepts('-5.5');   // false - negative string
 * $positiveValue->accepts(0.0);      // false - zero float
 *
 * @example
 * // Conversion examples
 * $positiveValue = new PositiveValue(new NumericValue());
 *
 * $positiveValue->convert(10);       // Returns 10
 * $positiveValue->convert('5.5');    // Returns 5.5
 * $positiveValue->convert(0.1);      // Returns 0.1
 * $positiveValue->convert('100');    // Returns 100
 *
 * @example
 * // E-commerce product validation
 * $productValue = new Map([
 *     'price' => new Gte('price', new PositiveValue(new NumericValue())),
 *     'quantity' => new Gte('stock', new PositiveValue(new IntValue())),
 *     'weight' => new Gt('weight', new PositiveValue(new FloatValue()))
 * ]);
 * // Ensures all product metrics are positive
 *
 * @example
 * // User profile validation
 * $profileValue = new All(
 *     new Gte('age', new PositiveValue(new IntValue())),
 *     new Gt('height_cm', new PositiveValue(new FloatValue())),
 *     new Gt('weight_kg', new PositiveValue(new FloatValue()))
 * );
 * // Ensures all physical measurements are positive
 *
 * @example
 * // Performance monitoring
 * $performanceValue = new PositiveValue(new FloatValue());
 * $monitorFilter = new All(
 *     new Lt('response_time', $performanceValue),    // Response time > 0
 *     new Gt('throughput', $performanceValue)        // Throughput > 0
 * );
 *
 * @example
 * // API validation
 * $positiveApiValue = new PositiveValue(new NumericValue());
 * if ($positiveApiValue->accepts($_POST['amount'])) {
 *     $validAmount = $positiveApiValue->convert($_POST['amount']);
 *     // Process valid positive amount
 * } else {
 *     // Handle invalid input - zero or negative
 * }
 *
 * @example
 * // Form validation
 * $ageValue = new PositiveValue(new IntValue());
 * if ($ageValue->accepts($_POST['age'])) {
 *     $validAge = $ageValue->convert($_POST['age']);
 *     // Process valid positive age
 * }
 *
 * @example
 * // Gaming score validation (positive scores only)
 * $scoreValue = new PositiveValue(new IntValue());
 * $gameFilter = new Gte('score', $scoreValue);
 * $result = $gameFilter->withValue(1500); // Valid - positive score
 * $result = $gameFilter->withValue(0);    // Invalid - zero score
 * $result = $gameFilter->withValue(-100); // Invalid - negative score
 *
 * @example
 * // Measurement validation
 * $distanceValue = new PositiveValue(new FloatValue());
 * $locationFilter = new Lt('distance_km', $distanceValue);
 * $result = $locationFilter->withValue(5.2);  // Valid - 5.2 km
 * $result = $locationFilter->withValue(0.0);  // Invalid - zero distance
 * $result = $locationFilter->withValue(-2.1); // Invalid - negative distance
 *
 * @example
 * // Complex validation with enum
 * $validQuantities = new EnumValue(
 *     new PositiveValue(new IntValue()),
 *     1, 2, 3, 5, 10 // Only these positive quantities allowed
 * );
 * $quantityFilter = new Equals('quantity', $validQuantities);
 * $result = $quantityFilter->withValue('3'); // Valid - positive quantity from enum
 *
 * @example
 * // Time duration validation
 * $durationValue = new PositiveValue(new FloatValue());
 * $taskFilter = new Gte('estimated_hours', $durationValue);
 * $result = $taskFilter->withValue(2.5);  // Valid - 2.5 hours
 * $result = $taskFilter->withValue(0.0);  // Invalid - zero duration
 * $result = $taskFilter->withValue(-1.0); // Invalid - negative duration
 *
 * @example
 * // Interest rate validation
 * $rateValue = new PositiveValue(new FloatValue());
 * $loanFilter = new Between('interest_rate', $rateValue);
 * $result = $loanFilter->withValue([0.5, 10.0]); // 0.5% to 10% (positive rates)
 * // Note: 0% interest would be rejected as it's not positive
 *
 * @example
 * // Temperature validation (Kelvin scale - must be positive)
 * $kelvinValue = new PositiveValue(new FloatValue());
 * $scienceFilter = new Gt('temperature_kelvin', $kelvinValue);
 * $result = $scienceFilter->withValue(273.15); // Valid - 0°C in Kelvin
 * $result = $scienceFilter->withValue(0);      // Invalid - absolute zero not positive
 *
 * @example
 * // Page number validation
 * $pageValue = new PositiveValue(new IntValue());
 * $paginationFilter = new Gte('page', $pageValue);
 * $result = $paginationFilter->withValue(1);  // Valid - first page
 * $result = $paginationFilter->withValue(10); // Valid - page 10
 * $result = $paginationFilter->withValue(0);  // Invalid - page 0 doesn't exist
 *
 * Important notes:
 * - Only accepts values that are strictly greater than zero (> 0)
 * - Zero is NOT considered positive (returns false)
 * - Uses the base ValueInterface for type conversion before comparison
 * - Useful for scenarios requiring meaningful positive values
 * - Different from NonNegativeValue (which allows zero, requires >= 0)
 * - Opposite of NegativeValue (which requires < 0)
 * - More restrictive than NonNegativeValue for cases where zero is not meaningful
 */
final class PositiveValue extends CompareValue
{
    protected function compare(mixed $value): bool
    {
        return $value > 0;
    }
}
