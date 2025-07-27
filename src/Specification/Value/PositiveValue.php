<?php

declare(strict_types=1);

namespace Spiral\DataGrid\Specification\Value;

/**
 * Validates values that are strictly greater than zero (positive numbers).
 * This is a concrete implementation of CompareValue that only accepts positive values.
 *
 * ```
 * // Product price validation
 * $priceValue = new PositiveValue(new NumericValue());
 * $productFilter = new Gte('price', $priceValue);
 * $result = $productFilter->withValue(19.99); // Valid - positive price
 * $result = $productFilter->withValue(0);     // Invalid - zero price
 * $result = $productFilter->withValue(-5);    // Invalid - negative price
 * ```
 * ```
 * // Order quantity validation
 * $quantityValue = new PositiveValue(new IntValue());
 * $orderFilter = new Gte('quantity', $quantityValue);
 * $result = $orderFilter->withValue(3);  // Valid - ordering 3 items
 * $result = $orderFilter->withValue(0);  // Invalid - zero quantity
 * $result = $orderFilter->withValue(-1); // Invalid - negative quantity
 * ```
 * ```
 * // User age validation (must be positive)
 * $ageValue = new PositiveValue(new IntValue());
 * $userFilter = new Gte('age', $ageValue);
 * $result = $ageValue->withValue(25); // Valid - 25 years old
 * $result = $ageValue->withValue(0);  // Invalid - zero age not meaningful
 * $result = $ageValue->withValue(-5); // Invalid - negative age impossible
 * ```
 * ```
 * // Database ID validation
 * $idValue = new PositiveValue(new IntValue());
 * $recordFilter = new Equals('user_id', $idValue);
 * $result = $recordFilter->withValue(123); // Valid - positive ID
 * $result = $recordFilter->withValue(0);   // Invalid - zero ID typically not used
 * $result = $recordFilter->withValue(-1);  // Invalid - negative ID not valid
 * ```
 * ```
 * // Performance metrics validation
 * $responseTimeValue = new PositiveValue(new FloatValue());
 * $performanceFilter = new Lt('response_time_ms', $responseTimeValue);
 * $result = $performanceFilter->withValue(150.5); // Valid - positive response time
 * $result = $performanceFilter->withValue(0.0);   // Invalid - zero response time
 * $result = $performanceFilter->withValue(-10.0); // Invalid - negative time impossible
 * ```
 * ```
 * // Star rating validation (1-5 stars, must be positive)
 * $ratingValue = new PositiveValue(new IntValue());
 * $reviewFilter = new Between('rating', $ratingValue);
 * $result = $reviewFilter->withValue([1, 5]); // Valid - 1 to 5 stars
 * $result = $reviewFilter->withValue([0, 5]); // Invalid - 0 stars not positive
 * ```
 * ```
 * // Weight validation
 * $weightValue = new PositiveValue(new FloatValue());
 * $shippingFilter = new Lt('weight_kg', $weightValue);
 * $result = $shippingFilter->withValue(2.5);  // Valid - 2.5kg
 * $result = $shippingFilter->withValue(0.0);  // Invalid - zero weight
 * $result = $shippingFilter->withValue(-1.0); // Invalid - negative weight
 * ```
 * ```
 * // Financial transaction amount
 * $amountValue = new PositiveValue(new NumericValue());
 * $transactionFilter = new Gte('amount', $amountValue);
 * $result = $transactionFilter->withValue(100.50); // Valid - positive amount
 * $result = $transactionFilter->withValue(0);      // Invalid - zero transaction
 * $result = $transactionFilter->withValue(-25);    // Invalid - negative amount
 * ```
 * ```
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
 * ```
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
