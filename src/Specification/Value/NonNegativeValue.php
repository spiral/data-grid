<?php

declare(strict_types=1);

namespace Spiral\DataGrid\Specification\Value;

/**
 * Validates values that are greater than or equal to zero (non-negative numbers).
 * This is a concrete implementation of CompareValue that accepts zero and positive values.
 *
 * Use cases:
 * - Stock level validation: Prevent negative inventory
 * - Age verification: Ensure age is 0 or positive
 * - Price validation: Free (0) or paid items only
 * - Quantity validation: Order quantities must be non-negative
 * - Performance monitoring: Metrics that can't be negative
 *
 * ```
 * // Inventory stock validation
 * $stockValue = new NonNegativeValue(new IntValue());
 * $inventoryFilter = new Gte('stock_quantity', $stockValue);
 * $result = $inventoryFilter->withValue(0);   // Valid - out of stock
 * $result = $inventoryFilter->withValue(100); // Valid - in stock
 * $result = $inventoryFilter->withValue(-5);  // Invalid - negative stock not allowed
 * ```
 * ```
 * // Age validation system
 * $ageValue = new NonNegativeValue(new IntValue());
 * $userFilter = new Gte('age', $ageValue);
 * $result = $userFilter->withValue(0);   // Valid - newborn
 * $result = $userFilter->withValue(25);  // Valid - adult
 * $result = $userFilter->withValue(-1);  // Invalid - negative age impossible
 * ```
 * ```
 * // Product pricing validation
 * $priceValue = new NonNegativeValue(new NumericValue());
 * $productFilter = new Gte('price', $priceValue);
 * $result = $productFilter->withValue(0);      // Valid - free item
 * $result = $productFilter->withValue(19.99);  // Valid - paid item
 * $result = $productFilter->withValue(-5.00);  // Invalid - negative price not allowed
 * ```
 * ```
 * // Order quantity validation
 * $quantityValue = new NonNegativeValue(new IntValue());
 * $orderFilter = new Gte('quantity', $quantityValue);
 * $result = $orderFilter->withValue(0);  // Valid - removing item (quantity 0)
 * $result = $orderFilter->withValue(5);  // Valid - ordering 5 items
 * $result = $orderFilter->withValue(-2); // Invalid - negative quantity
 * ```
 * ```
 * // Performance metrics validation
 * $responseTimeValue = new NonNegativeValue(new FloatValue());
 * $performanceFilter = new Lt('response_time_ms', $responseTimeValue);
 * $result = $performanceFilter->withValue(0.0);    // Valid - instant response
 * $result = $performanceFilter->withValue(150.5);  // Valid - normal response time
 * $result = $performanceFilter->withValue(-10.0);  // Invalid - negative time impossible
 * ```
 * ```
 * // Account balance validation (basic accounts)
 * $balanceValue = new NonNegativeValue(new NumericValue());
 * $accountFilter = new Gte('account_balance', $balanceValue);
 * $result = $accountFilter->withValue(0.00);     // Valid - zero balance
 * $result = $accountFilter->withValue(1000.50);  // Valid - positive balance
 * $result = $accountFilter->withValue(-100.00);  // Invalid - overdraft not allowed
 * ```
 * ```
 * // Distance and measurement validation
 * $distanceValue = new NonNegativeValue(new FloatValue());
 * $locationFilter = new Gte('distance_km', $distanceValue);
 * $result = $locationFilter->withValue(0.0);   // Valid - same location
 * $result = $locationFilter->withValue(15.7);  // Valid - 15.7 km away
 * $result = $locationFilter->withValue(-5.2);  // Invalid - negative distance impossible
 * ```
 * ```
 * // Weight and physical measurements
 * $weightValue = new NonNegativeValue(new FloatValue());
 * $shippingFilter = new Lt('weight_kg', $weightValue);
 * $result = $shippingFilter->withValue(0.0);  // Valid - weightless item
 * $result = $shippingFilter->withValue(2.5);  // Valid - 2.5 kg package
 * $result = $shippingFilter->withValue(-1.0); // Invalid - negative weight impossible
 * ```
 * ```
 * // Validation examples
 * $nonNegativeValue = new NonNegativeValue(new NumericValue());
 *
 * // Valid inputs (zero or positive)
 * $nonNegativeValue->accepts(0);        // true - zero allowed
 * $nonNegativeValue->accepts(10);       // true - positive allowed
 * $nonNegativeValue->accepts('0');      // true - zero string
 * $nonNegativeValue->accepts('5.5');    // true - positive string
 * $nonNegativeValue->accepts(0.0);      // true - zero float
 * $nonNegativeValue->accepts(100.5);    // true - positive float
 *
 * // Invalid inputs (negative)
 * $nonNegativeValue->accepts(-1);       // false - negative not allowed
 * $nonNegativeValue->accepts('-5.5');   // false - negative string
 * $nonNegativeValue->accepts(-0.001);   // false - small negative
 * ```
 * ```
 * // Conversion examples
 * $nonNegativeValue = new NonNegativeValue(new NumericValue());
 *
 * $nonNegativeValue->convert(0);        // Returns 0
 * $nonNegativeValue->convert('10');     // Returns 10
 * $nonNegativeValue->convert(5.5);      // Returns 5.5
 * $nonNegativeValue->convert('0.0');    // Returns 0.0
 * ```
 *
 * Important notes:
 * - Accepts values that are greater than or equal to zero (>= 0)
 * - Zero is explicitly allowed and considered valid
 * - Uses the base ValueInterface for type conversion before comparison
 * - Useful for quantities, measurements, and counts that can't be negative
 * - Different from PositiveValue (which excludes zero, requires > 0)
 * - Opposite of NegativeValue (which requires < 0)
 * - More permissive than PositiveValue for cases where zero is meaningful
 */
final class NonNegativeValue extends CompareValue
{
    protected function compare(mixed $value): bool
    {
        return $value >= 0;
    }
}
