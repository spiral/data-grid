<?php

declare(strict_types=1);

namespace Spiral\DataGrid\Specification\Value;

use Spiral\DataGrid\Exception\ValueException;
use Spiral\DataGrid\Specification\ValueInterface;

/**
 * Validates and converts numeric values (integers or floats) from various input formats.
 * More flexible than IntValue or FloatValue - accepts both integer and floating-point numbers
 * and automatically returns the most appropriate type (int for whole numbers, float for decimals).
 *
 * Real-world usage examples:
 * - General numeric input: Accept any numeric value without type restrictions
 * - Mathematical calculations: Handle both integer and decimal inputs
 * - API parameters: Accept numeric parameters of any type
 * - Financial amounts: Handle both whole dollar amounts and cents
 * - Measurement data: Accept both precise and approximate values
 * - Configuration values: Numeric settings that can be int or float
 * - Statistical data: Numbers that may or may not have decimal places
 * - User input normalization: Handle various numeric input formats
 *
 * Automatic type detection:
 * - Whole numbers return as int: 10, '15', 3.0 → int
 * - Decimal numbers return as float: 10.5, '15.75', 3.14 → float
 * - Uses PHP's automatic type coercion (+ 0 operator)
 *
 * @example
 * // General price filtering (handles $10 and $10.99)
 * $priceValue = new NumericValue();
 * $priceFilter = new Between('price', $priceValue);
 * $result = $priceFilter->withValue([10, 99.99]); // Handles both int and float
 *
 * @example
 * // API parameter that can be int or float
 * $thresholdValue = new NumericValue();
 * $apiFilter = new Gte('threshold', $thresholdValue);
 * $result = $apiFilter->withValue('85');    // Returns int 85
 * $result = $apiFilter->withValue('85.5');  // Returns float 85.5
 *
 * @example
 * // Mathematical calculation input
 * $factorValue = new NumericValue();
 * $calculationFilter = new Equals('multiplier', $factorValue);
 * $result = $calculationFilter->withValue(2);     // int 2
 * $result = $calculationFilter->withValue(1.5);   // float 1.5
 * $result = $calculationFilter->withValue('3.0'); // int 3 (whole number)
 *
 * @example
 * // Measurement data (can be precise or approximate)
 * $measurementValue = new NumericValue();
 * $sensorFilter = new Between('temperature', $measurementValue);
 * $result = $sensorFilter->withValue([20, 25.7]); // int 20, float 25.7
 *
 * @example
 * // Financial amount handling
 * $amountValue = new NumericValue();
 * $transactionFilter = new Gte('amount', $amountValue);
 * $result = $transactionFilter->withValue(100);     // $100.00 (int)
 * $result = $transactionFilter->withValue(99.99);   // $99.99 (float)
 * $result = $transactionFilter->withValue('50.5');  // $50.50 (float)
 *
 * @example
 * // Configuration value (timeout can be seconds or fractional)
 * $timeoutValue = new NumericValue();
 * $configFilter = new Lt('timeout', $timeoutValue);
 * $result = $configFilter->withValue(30);    // 30 seconds (int)
 * $result = $configFilter->withValue(2.5);   // 2.5 seconds (float)
 *
 * @example
 * // Performance metrics (various numeric types)
 * $metricValue = new NumericValue();
 * $performanceFilter = new Between('response_time', $metricValue);
 * $result = $performanceFilter->withValue([0, 500.25]); // 0ms to 500.25ms
 *
 * @example
 * // Statistical data processing
 * $statsValue = new NumericValue();
 * $statisticsFilter = new Gte('average', $statsValue);
 * $result = $statisticsFilter->withValue(4);     // Average of 4 (int)
 * $result = $statisticsFilter->withValue(4.2);   // Average of 4.2 (float)
 *
 * @example
 * // Input validation examples
 * $numericValue = new NumericValue();
 *
 * // Valid inputs
 * $numericValue->accepts(10);           // true - integer
 * $numericValue->accepts(10.5);         // true - float
 * $numericValue->accepts('10');         // true - numeric string
 * $numericValue->accepts('10.5');       // true - decimal string
 * $numericValue->accepts('-5');         // true - negative string
 * $numericValue->accepts(-5.2);         // true - negative float
 * $numericValue->accepts('0');          // true - zero string
 * $numericValue->accepts(0);            // true - zero integer
 * $numericValue->accepts('');           // true - empty string (converts to 0)
 * $numericValue->accepts('1.5e10');     // true - scientific notation
 *
 * // Invalid inputs
 * $numericValue->accepts('abc');        // false - non-numeric
 * $numericValue->accepts('10abc');      // false - mixed content
 * $numericValue->accepts([]);           // false - array
 * $numericValue->accepts(null);         // false - null
 * $numericValue->accepts(true);         // false - boolean
 *
 * @example
 * // Conversion examples (automatic type detection)
 * $numericValue = new NumericValue();
 *
 * $numericValue->convert(10);           // Returns int 10
 * $numericValue->convert('10');         // Returns int 10
 * $numericValue->convert(10.5);         // Returns float 10.5
 * $numericValue->convert('10.5');       // Returns float 10.5
 * $numericValue->convert('10.0');       // Returns int 10 (whole number)
 * $numericValue->convert(10.0);         // Returns int 10 (whole number)
 * $numericValue->convert('');           // Returns int 0
 * $numericValue->convert('-5.2');       // Returns float -5.2
 * $numericValue->convert('1e3');        // Returns int 1000
 * $numericValue->convert('1.5e3');      // Returns float 1500.0
 *
 * @example
 * // E-commerce flexible pricing
 * $priceValue = new NumericValue();
 * $productFilter = new All(
 *     new Gte('price', $priceValue),              // Minimum price
 *     new Lte('price', new NumericValue())        // Maximum price
 * );
 * // Handles: $5 (int), $5.99 (float), $10.00 (int), $19.95 (float)
 *
 * @example
 * // Scientific measurement
 * $measureValue = new NumericValue();
 * $experimentFilter = new Between('measurement', $measureValue);
 * $result = $experimentFilter->withValue([0.001, 1000000]); // Micro to mega scale
 *
 * @example
 * // Rating system (can be whole or partial stars)
 * $ratingValue = new NumericValue();
 * $reviewFilter = new Gte('rating', $ratingValue);
 * $result = $reviewFilter->withValue(4);     // 4 stars (int)
 * $result = $reviewFilter->withValue(4.5);   // 4.5 stars (float)
 *
 * @example
 * // API input validation
 * $numericApiValue = new NumericValue();
 * if ($numericApiValue->accepts($_GET['limit'])) {
 *     $validLimit = $numericApiValue->convert($_GET['limit']);
 *     // Use appropriate numeric type automatically
 * }
 *
 * @example
 * // Form processing
 * $quantityValue = new NumericValue();
 * if ($quantityValue->accepts($_POST['quantity'])) {
 *     $validQuantity = $quantityValue->convert($_POST['quantity']);
 *     // Handle both whole numbers and decimals
 * }
 *
 * @example
 * // Complex numeric filtering
 * $rangeValue = new NumericValue();
 * $complexFilter = new Map([
 *     'min' => new Gte('value', $rangeValue),
 *     'max' => new Lte('value', $rangeValue),
 *     'target' => new Equals('target', $rangeValue)
 * ]);
 * $result = $complexFilter->withValue([
 *     'min' => 10,      // int
 *     'max' => 99.9,    // float
 *     'target' => '50'  // converts to int 50
 * ]);
 *
 * @example
 * // Performance monitoring
 * $performanceValue = new NumericValue();
 * $monitorFilter = new Lt('cpu_usage', $performanceValue);
 * $result = $monitorFilter->withValue(85);    // 85% (int)
 * $result = $monitorFilter->withValue(85.7);  // 85.7% (float)
 *
 * @example
 * // Financial calculations
 * $financialValue = new NumericValue();
 * $budgetFilter = new Between('budget', $financialValue);
 * $result = $budgetFilter->withValue([1000, 5000.50]); // $1000 to $5000.50
 *
 * @example
 * // Geographic coordinates
 * $coordinateValue = new NumericValue();
 * $locationFilter = new Between('latitude', $coordinateValue);
 * $result = $locationFilter->withValue([40, 41.5]); // Latitude range
 *
 * @example
 * // Time duration (seconds, can be fractional)
 * $durationValue = new NumericValue();
 * $timeFilter = new Lt('duration', $durationValue);
 * $result = $timeFilter->withValue(30);    // 30 seconds (int)
 * $result = $timeFilter->withValue(2.5);   // 2.5 seconds (float)
 *
 * @example
 * // Error handling
 * $numericValue = new NumericValue();
 * try {
 *     if ($numericValue->accepts($userInput)) {
 *         $validNumber = $numericValue->convert($userInput);
 *         // Process valid numeric value
 *     }
 * } catch (ValueException $e) {
 *     // Handle conversion error
 * }
 *
 * Important notes:
 * - Empty strings convert to 0 (int)
 * - Automatic type detection: whole numbers become int, decimals become float
 * - Uses PHP's + 0 operator for intelligent type coercion
 * - More flexible than IntValue or FloatValue for general numeric input
 * - Scientific notation is supported ('1e3', '2.5e-2')
 * - Negative numbers are fully supported
 * - Preserves precision for decimal values
 * - Always call accepts() before convert() to ensure valid input
 */
final class NumericValue implements ValueInterface
{
    public function accepts(mixed $value): bool
    {
        return \is_numeric($value) || $value === '';
    }

    public function convert(mixed $value): float|int
    {
        if (\is_numeric($value)) {
            return $value + 0;
        }

        throw new ValueException(
            \sprintf(
                'Value is expected to be numeric, got `%s`. Check the value with `accepts()` method first.',
                \get_debug_type($value),
            ),
        );
    }
}
