<?php

declare(strict_types=1);

namespace Spiral\DataGrid\Specification\Value;

use Spiral\DataGrid\Specification\ValueInterface;

/**
 * Validates and converts floating-point number values.
 * Accepts numeric values or empty strings and converts them to float type.
 *
 * Real-world usage examples:
 * - Price calculations: Product prices, tax amounts, discounts
 * - Measurements: Weight, height, dimensions, distances
 * - Financial data: Exchange rates, interest rates, percentages
 * - Scientific data: Temperature, coordinates, sensor readings
 * - Performance metrics: Response times, throughput rates, percentages
 * - Rating systems: Star ratings (4.5/5), decimal scores
 * - Geographic coordinates: Latitude, longitude with decimal precision
 * - Statistical data: Averages, percentages, ratios with decimal places
 *
 * Precision considerations:
 * - Maintains decimal precision for accurate calculations
 * - Suitable for monetary values (though Decimal type preferred for finance)
 * - Handles scientific notation (1.5e10)
 * - Accepts both integer and float inputs
 *
 * @example
 * // Product price filtering
 * $priceValue = new FloatValue();
 * $priceFilter = new Between('price', $priceValue);
 * $result = $priceFilter->withValue([10.99, 199.99]); // Price range
 *
 * @example
 * // Geographic coordinate filtering
 * $latitudeValue = new FloatValue();
 * $locationFilter = new Between('latitude', $latitudeValue);
 * $result = $locationFilter->withValue([40.7128, 40.7614]); // NYC area
 *
 * @example
 * // Rating system
 * $ratingValue = new FloatValue();
 * $reviewFilter = new Gte('average_rating', $ratingValue);
 * $result = $reviewFilter->withValue(4.5); // 4.5 stars or higher
 *
 * @example
 * // Performance metrics
 * $responseTimeValue = new FloatValue();
 * $performanceFilter = new Lt('response_time', $responseTimeValue);
 * $result = $performanceFilter->withValue(0.250); // Under 250ms
 *
 * @example
 * // Financial calculations
 * $interestRateValue = new FloatValue();
 * $loanFilter = new Between('interest_rate', $interestRateValue);
 * $result = $loanFilter->withValue([2.5, 5.0]); // 2.5% to 5.0%
 *
 * @example
 * // Scientific measurements
 * $temperatureValue = new FloatValue();
 * $weatherFilter = new Between('temperature_celsius', $temperatureValue);
 * $result = $weatherFilter->withValue([-10.5, 35.8]); // Temperature range
 *
 * @example
 * // E-commerce discount system
 * $discountValue = new FloatValue();
 * $discountFilter = new Gt('discount_percentage', $discountValue);
 * $result = $discountFilter->withValue(15.0); // More than 15% off
 *
 * @example
 * // Weight and dimension filtering
 * $weightValue = new FloatValue();
 * $shippingFilter = new Lte('weight_kg', $weightValue);
 * $result = $shippingFilter->withValue(2.5); // Under 2.5kg
 *
 * @example
 * // Input validation examples
 * $floatValue = new FloatValue();
 *
 * // Valid inputs
 * $floatValue->accepts(10.5);        // true - float
 * $floatValue->accepts(10);          // true - integer
 * $floatValue->accepts('10.5');      // true - numeric string
 * $floatValue->accepts('10');        // true - integer string
 * $floatValue->accepts('0.001');     // true - small decimal
 * $floatValue->accepts('1.5e10');    // true - scientific notation
 * $floatValue->accepts('');          // true - empty string
 * $floatValue->accepts(0);           // true - zero
 * $floatValue->accepts(0.0);         // true - float zero
 *
 * // Invalid inputs
 * $floatValue->accepts('abc');       // false - not numeric
 * $floatValue->accepts('10.5abc');   // false - mixed content
 * $floatValue->accepts([]);          // false - array
 * $floatValue->accepts(null);        // false - null
 * $floatValue->accepts(true);        // false - boolean
 *
 * @example
 * // Conversion examples
 * $floatValue = new FloatValue();
 *
 * $floatValue->convert(10);          // Returns 10.0 (float)
 * $floatValue->convert('10.5');      // Returns 10.5
 * $floatValue->convert('10');        // Returns 10.0
 * $floatValue->convert('');          // Returns 0.0
 * $floatValue->convert('1.5e2');     // Returns 150.0
 * $floatValue->convert(0);           // Returns 0.0
 *
 * @example
 * // Monetary calculations (be aware of precision)
 * $moneyValue = new FloatValue();
 * $orderFilter = new Gte('total_amount', $moneyValue);
 * $result = $orderFilter->withValue(99.99); // Orders $99.99 or more
 * // Note: For financial calculations, consider using a Decimal library
 *
 * @example
 * // API parameter validation
 * $apiFloatValue = new FloatValue();
 * // GET /api/products?min_rating=4.2&max_price=149.99
 * $ratingFilter = new Gte('rating', $apiFloatValue);
 * $priceFilter = new Lte('price', $apiFloatValue);
 * $ratingResult = $ratingFilter->withValue($_GET['min_rating']);
 * $priceResult = $priceFilter->withValue($_GET['max_price']);
 *
 * @example
 * // Form processing
 * $heightValue = new FloatValue();
 * if ($heightValue->accepts($_POST['height'])) {
 *     $validHeight = $heightValue->convert($_POST['height']);
 *     // Process height in meters (e.g., 1.75)
 * }
 *
 * @example
 * // Statistical analysis
 * $percentageValue = new FloatValue();
 * $statsFilter = new Between('success_rate', $percentageValue);
 * $result = $statsFilter->withValue([85.5, 95.0]); // 85.5% to 95% success rate
 *
 * @example
 * // Sensor data processing
 * $sensorValue = new FloatValue();
 * $sensorFilter = new All(
 *     new Gte('humidity', $sensorValue),     // Humidity >= 40.0%
 *     new Lte('humidity', $sensorValue)      // Humidity <= 80.0%
 * );
 * $result = $sensorFilter->withValue(65.5); // Valid humidity reading
 *
 * @example
 * // Gaming and scoring systems
 * $scoreValue = new FloatValue();
 * $gameFilter = new Gte('high_score', $scoreValue);
 * $result = $gameFilter->withValue(1247.85); // High score threshold
 *
 * @example
 * // Real estate pricing
 * $sqftPriceValue = new FloatValue();
 * $realEstateFilter = new Between('price_per_sqft', $sqftPriceValue);
 * $result = $realEstateFilter->withValue([150.00, 300.00]); // Price per sq ft
 *
 * @example
 * // Network performance monitoring
 * $latencyValue = new FloatValue();
 * $networkFilter = new Lt('avg_latency_ms', $latencyValue);
 * $result = $networkFilter->withValue(50.5); // Under 50.5ms latency
 *
 * @example
 * // Recipe and cooking measurements
 * $ingredientValue = new FloatValue();
 * $recipeFilter = new Equals('ingredient_amount', $ingredientValue);
 * $result = $recipeFilter->withValue(2.5); // 2.5 cups, liters, etc.
 *
 * Important notes:
 * - Empty strings convert to 0.0
 * - Uses PHP's (float) casting for conversion
 * - Floating-point precision limitations apply
 * - For financial calculations, consider decimal libraries
 * - Scientific notation is supported ('1.5e10')
 * - Accepts both positive and negative numbers
 * - More precise than NumericValue for decimal-specific needs
 */
final class FloatValue implements ValueInterface
{
    public function accepts(mixed $value): bool
    {
        return \is_numeric($value) || $value === '';
    }

    public function convert(mixed $value): float
    {
        return (float) $value;
    }
}
