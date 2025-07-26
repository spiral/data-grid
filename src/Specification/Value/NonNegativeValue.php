<?php

declare(strict_types=1);

namespace Spiral\DataGrid\Specification\Value;

/**
 * Validates values that are greater than or equal to zero (non-negative numbers).
 * This is a concrete implementation of CompareValue that accepts zero and positive values.
 *
 * Real-world usage examples:
 * - Inventory management: Stock quantities cannot be negative
 * - Age validation: Age cannot be negative (newborns are 0)
 * - Pricing systems: Prices must be non-negative (free items are 0)
 * - Time durations: Processing time, wait time cannot be negative
 * - Counting systems: Item counts, user counts must be non-negative
 * - Performance metrics: Response times, throughput rates must be non-negative
 * - Financial balances: Account balances typically non-negative (overdraft rules)
 * - Physical measurements: Distance, weight, height cannot be negative
 *
 * Use cases:
 * - Stock level validation: Prevent negative inventory
 * - Age verification: Ensure age is 0 or positive
 * - Price validation: Free (0) or paid items only
 * - Quantity validation: Order quantities must be non-negative
 * - Performance monitoring: Metrics that can't be negative
 *
 * @example
 * // Inventory stock validation
 * $stockValue = new NonNegativeValue(new IntValue());
 * $inventoryFilter = new Gte('stock_quantity', $stockValue);
 * $result = $inventoryFilter->withValue(0);   // Valid - out of stock
 * $result = $inventoryFilter->withValue(100); // Valid - in stock
 * $result = $inventoryFilter->withValue(-5);  // Invalid - negative stock not allowed
 *
 * @example
 * // Age validation system
 * $ageValue = new NonNegativeValue(new IntValue());
 * $userFilter = new Gte('age', $ageValue);
 * $result = $userFilter->withValue(0);   // Valid - newborn
 * $result = $userFilter->withValue(25);  // Valid - adult
 * $result = $userFilter->withValue(-1);  // Invalid - negative age impossible
 *
 * @example
 * // Product pricing validation
 * $priceValue = new NonNegativeValue(new NumericValue());
 * $productFilter = new Gte('price', $priceValue);
 * $result = $productFilter->withValue(0);      // Valid - free item
 * $result = $productFilter->withValue(19.99);  // Valid - paid item
 * $result = $productFilter->withValue(-5.00);  // Invalid - negative price not allowed
 *
 * @example
 * // Order quantity validation
 * $quantityValue = new NonNegativeValue(new IntValue());
 * $orderFilter = new Gte('quantity', $quantityValue);
 * $result = $orderFilter->withValue(0);  // Valid - removing item (quantity 0)
 * $result = $orderFilter->withValue(5);  // Valid - ordering 5 items
 * $result = $orderFilter->withValue(-2); // Invalid - negative quantity
 *
 * @example
 * // Performance metrics validation
 * $responseTimeValue = new NonNegativeValue(new FloatValue());
 * $performanceFilter = new Lt('response_time_ms', $responseTimeValue);
 * $result = $performanceFilter->withValue(0.0);    // Valid - instant response
 * $result = $performanceFilter->withValue(150.5);  // Valid - normal response time
 * $result = $performanceFilter->withValue(-10.0);  // Invalid - negative time impossible
 *
 * @example
 * // Account balance validation (basic accounts)
 * $balanceValue = new NonNegativeValue(new NumericValue());
 * $accountFilter = new Gte('account_balance', $balanceValue);
 * $result = $accountFilter->withValue(0.00);     // Valid - zero balance
 * $result = $accountFilter->withValue(1000.50);  // Valid - positive balance
 * $result = $accountFilter->withValue(-100.00);  // Invalid - overdraft not allowed
 *
 * @example
 * // Distance and measurement validation
 * $distanceValue = new NonNegativeValue(new FloatValue());
 * $locationFilter = new Gte('distance_km', $distanceValue);
 * $result = $locationFilter->withValue(0.0);   // Valid - same location
 * $result = $locationFilter->withValue(15.7);  // Valid - 15.7 km away
 * $result = $locationFilter->withValue(-5.2);  // Invalid - negative distance impossible
 *
 * @example
 * // Weight and physical measurements
 * $weightValue = new NonNegativeValue(new FloatValue());
 * $shippingFilter = new Lt('weight_kg', $weightValue);
 * $result = $shippingFilter->withValue(0.0);  // Valid - weightless item
 * $result = $shippingFilter->withValue(2.5);  // Valid - 2.5 kg package
 * $result = $shippingFilter->withValue(-1.0); // Invalid - negative weight impossible
 *
 * @example
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
 *
 * @example
 * // Conversion examples
 * $nonNegativeValue = new NonNegativeValue(new NumericValue());
 *
 * $nonNegativeValue->convert(0);        // Returns 0
 * $nonNegativeValue->convert('10');     // Returns 10
 * $nonNegativeValue->convert(5.5);      // Returns 5.5
 * $nonNegativeValue->convert('0.0');    // Returns 0.0
 *
 * @example
 * // E-commerce cart validation
 * $cartQuantityValue = new NonNegativeValue(new IntValue());
 * $cartFilter = new Map([
 *     'quantity' => new Gte('quantity', $cartQuantityValue),
 *     'price' => new Gte('unit_price', new NonNegativeValue(new NumericValue()))
 * ]);
 * $result = $cartFilter->withValue(['quantity' => 3, 'price' => 29.99]);
 *
 * @example
 * // Time duration validation
 * $durationValue = new NonNegativeValue(new IntValue());
 * $taskFilter = new Gte('estimated_hours', $durationValue);
 * $result = $taskFilter->withValue(0);  // Valid - instant task
 * $result = $taskFilter->withValue(8);  // Valid - 8-hour task
 * $result = $taskFilter->withValue(-2); // Invalid - negative time
 *
 * @example
 * // Score and rating validation
 * $scoreValue = new NonNegativeValue(new IntValue());
 * $gameFilter = new Gte('player_score', $scoreValue);
 * $result = $gameFilter->withValue(0);     // Valid - starting score
 * $result = $gameFilter->withValue(1500);  // Valid - earned points
 * $result = $gameFilter->withValue(-100);  // Invalid - negative score not allowed
 *
 * @example
 * // API validation for non-negative values
 * $nonNegativeApiValue = new NonNegativeValue(new NumericValue());
 * if ($nonNegativeApiValue->accepts($_POST['amount'])) {
 *     $validAmount = $nonNegativeApiValue->convert($_POST['amount']);
 *     // Process valid non-negative amount
 * } else {
 *     // Handle invalid input - negative value
 * }
 *
 * @example
 * // Form validation
 * $ageValue = new NonNegativeValue(new IntValue());
 * if ($ageValue->accepts($_POST['age'])) {
 *     $validAge = $ageValue->convert($_POST['age']);
 *     if ($validAge === 0) {
 *         // Handle newborn case
 *     } else {
 *         // Handle normal age
 *     }
 * }
 *
 * @example
 * // Complex validation with range
 * $stockLevelValue = new NonNegativeValue(new IntValue());
 * $stockAlert = new All(
 *     new Gte('current_stock', $stockLevelValue),  // Must be non-negative
 *     new Lt('current_stock', 10)                  // Low stock alert under 10
 * );
 * $result = $stockAlert->withValue(5); // Valid - low stock (5 items)
 *
 * @example
 * // Database record validation
 * $idValue = new NonNegativeValue(new IntValue());
 * $recordFilter = new Gte('record_id', $idValue);
 * $result = $recordFilter->withValue(0);    // Valid - system records can be ID 0
 * $result = $recordFilter->withValue(12345); // Valid - normal record ID
 * $result = $recordFilter->withValue(-1);   // Invalid - negative ID not allowed
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
