<?php

declare(strict_types=1);

namespace Spiral\DataGrid\Specification\Value;

use Spiral\DataGrid\Specification\ValueInterface;

/**
 * Validates and converts floating-point number values.
 * Accepts numeric values or empty strings and converts them to float type.
 *
 * ```
 * // Product price filtering
 * $priceValue = new FloatValue();
 * $priceFilter = new Between('price', $priceValue);
 * $result = $priceFilter->withValue([10.99, 199.99]); // Price range
 * ```
 * ```
 * // Geographic coordinate filtering
 * $latitudeValue = new FloatValue();
 * $locationFilter = new Between('latitude', $latitudeValue);
 * $result = $locationFilter->withValue([40.7128, 40.7614]); // NYC area
 * ```
 * ```
 * // Rating system
 * $ratingValue = new FloatValue();
 * $reviewFilter = new Gte('average_rating', $ratingValue);
 * $result = $reviewFilter->withValue(4.5); // 4.5 stars or higher
 * ```
 * ```
 * // Performance metrics
 * $responseTimeValue = new FloatValue();
 * $performanceFilter = new Lt('response_time', $responseTimeValue);
 * $result = $performanceFilter->withValue(0.250); // Under 250ms
 * ```
 * ```
 * // Financial calculations
 * $interestRateValue = new FloatValue();
 * $loanFilter = new Between('interest_rate', $interestRateValue);
 * $result = $loanFilter->withValue([2.5, 5.0]); // 2.5% to 5.0%
 * ```
 * ```
 * // Scientific measurements
 * $temperatureValue = new FloatValue();
 * $weatherFilter = new Between('temperature_celsius', $temperatureValue);
 * $result = $weatherFilter->withValue([-10.5, 35.8]); // Temperature range
 * ```
 * ```
 * // E-commerce discount system
 * $discountValue = new FloatValue();
 * $discountFilter = new Gt('discount_percentage', $discountValue);
 * $result = $discountFilter->withValue(15.0); // More than 15% off
 * ```
 * ```
 * // Weight and dimension filtering
 * $weightValue = new FloatValue();
 * $shippingFilter = new Lte('weight_kg', $weightValue);
 * $result = $shippingFilter->withValue(2.5); // Under 2.5kg
 * ```
 * ```
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
 * ```
 * ```
 * // Conversion examples
 * $floatValue = new FloatValue();
 *
 * $floatValue->convert(10);          // Returns 10.0 (float)
 * $floatValue->convert('10.5');      // Returns 10.5
 * $floatValue->convert('10');        // Returns 10.0
 * $floatValue->convert('');          // Returns 0.0
 * $floatValue->convert('1.5e2');     // Returns 150.0
 * $floatValue->convert(0);           // Returns 0.0
 * ```
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
