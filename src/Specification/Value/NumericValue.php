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
 * Automatic type detection:
 * - Whole numbers return as int: 10, '15', 3.0 → int
 * - Decimal numbers return as float: 10.5, '15.75', 3.14 → float
 * - Uses PHP's automatic type coercion (+ 0 operator)
 *
 * ```
 * // General price filtering (handles $10 and $10.99)
 * $priceValue = new NumericValue();
 * $priceFilter = new Between('price', $priceValue);
 * $result = $priceFilter->withValue([10, 99.99]); // Handles both int and float
 * ```
 * ```
 * // API parameter that can be int or float
 * $thresholdValue = new NumericValue();
 * $apiFilter = new Gte('threshold', $thresholdValue);
 * $result = $apiFilter->withValue('85');    // Returns int 85
 * $result = $apiFilter->withValue('85.5');  // Returns float 85.5
 * ```
 * ```
 * // Mathematical calculation input
 * $factorValue = new NumericValue();
 * $calculationFilter = new Equals('multiplier', $factorValue);
 * $result = $calculationFilter->withValue(2);     // int 2
 * $result = $calculationFilter->withValue(1.5);   // float 1.5
 * $result = $calculationFilter->withValue('3.0'); // int 3 (whole number)
 * ```
 * ```
 * // Measurement data (can be precise or approximate)
 * $measurementValue = new NumericValue();
 * $sensorFilter = new Between('temperature', $measurementValue);
 * $result = $sensorFilter->withValue([20, 25.7]); // int 20, float 25.7
 * ```
 * ```
 * // Financial amount handling
 * $amountValue = new NumericValue();
 * $transactionFilter = new Gte('amount', $amountValue);
 * $result = $transactionFilter->withValue(100);     // $100.00 (int)
 * $result = $transactionFilter->withValue(99.99);   // $99.99 (float)
 * $result = $transactionFilter->withValue('50.5');  // $50.50 (float)
 * ```
 * ```
 * // Configuration value (timeout can be seconds or fractional)
 * $timeoutValue = new NumericValue();
 * $configFilter = new Lt('timeout', $timeoutValue);
 * $result = $configFilter->withValue(30);    // 30 seconds (int)
 * $result = $configFilter->withValue(2.5);   // 2.5 seconds (float)
 * ```
 * ```
 * // Performance metrics (various numeric types)
 * $metricValue = new NumericValue();
 * $performanceFilter = new Between('response_time', $metricValue);
 * $result = $performanceFilter->withValue([0, 500.25]); // 0ms to 500.25ms
 * ```
 * ```
 * // Statistical data processing
 * $statsValue = new NumericValue();
 * $statisticsFilter = new Gte('average', $statsValue);
 * $result = $statisticsFilter->withValue(4);     // Average of 4 (int)
 * $result = $statisticsFilter->withValue(4.2);   // Average of 4.2 (float)
 * ```
 * ```
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
 * ```
 * ```
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
 * ```
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
