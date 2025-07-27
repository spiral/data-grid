<?php

declare(strict_types=1);

namespace Spiral\DataGrid\Specification\Value;

use Spiral\DataGrid\Specification\ValueInterface;

/**
 * Validates string or numeric values against a regular expression pattern.
 * Ensures input matches the specified regex pattern and converts to string format.
 *
 * ```
 * // Email validation
 * $emailValue = new RegexValue('/^[^@]+@[^@]+\.[^@]+$/');
 * $emailFilter = new Equals('email', $emailValue);
 * $result = $emailFilter->withValue('user@site.com'); // Valid email
 * $result = $emailFilter->withValue('invalid-email');    // Invalid - no @ or domain
 * ```
 * ```
 * // Phone number validation (international format)
 * $phoneValue = new RegexValue('/^\+?[1-9]\d{1,14}$/');
 * $contactFilter = new Equals('phone', $phoneValue);
 * $result = $contactFilter->withValue('+1234567890');  // Valid international format
 * $result = $contactFilter->withValue('123-456-7890'); // Invalid - contains dashes
 * ```
 * ```
 * // Password strength validation
 * $passwordValue = new RegexValue('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/');
 * $userFilter = new Equals('password', $passwordValue);
 * $result = $userFilter->withValue('StrongPass123'); // Valid - meets all requirements
 * $result = $userFilter->withValue('weakpass');      // Invalid - no uppercase/digits
 * ```
 * ```
 * // SKU/Product code validation
 * $skuValue = new RegexValue('/^[A-Z]{2,3}-\d{4,6}$/');
 * $productFilter = new Equals('sku', $skuValue);
 * $result = $productFilter->withValue('ABC-12345');  // Valid SKU format
 * $result = $productFilter->withValue('abc-123');    // Invalid - lowercase, too short
 * ```
 * ```
 * // Postal code validation (US ZIP codes)
 * $zipValue = new RegexValue('/^\d{5}(-\d{4})?$/');
 * $addressFilter = new Equals('zip_code', $zipValue);
 * $result = $zipFilter->withValue('12345');      // Valid - 5 digit ZIP
 * $result = $zipFilter->withValue('12345-6789'); // Valid - ZIP+4 format
 * $result = $zipFilter->withValue('ABC123');     // Invalid - contains letters
 * ```
 * ```
 * // License plate validation
 * $plateValue = new RegexValue('/^[A-Z0-9]{6,8}$/');
 * $vehicleFilter = new Equals('license_plate', $plateValue);
 * $result = $vehicleFilter->withValue('ABC1234');  // Valid plate
 * $result = $plateFilter->withValue('AB-123');     // Invalid - contains dash
 * ```
 * ```
 * // Date format validation (YYYY-MM-DD)
 * $dateValue = new RegexValue('/^\d{4}-\d{2}-\d{2}$/');
 * $eventFilter = new Equals('event_date', $dateValue);
 * $result = $eventFilter->withValue('2024-01-15');  // Valid date format
 * $result = $eventFilter->withValue('01/15/2024');  // Invalid - wrong format
 * ```
 * ```
 * // Validation examples
 * $regexValue = new RegexValue('/^[A-Z]{2}\d{4}$/'); // 2 letters + 4 digits
 *
 * // Valid inputs
 * $regexValue->accepts('AB1234');    // true - matches pattern
 * $regexValue->accepts('XY9876');    // true - matches pattern
 * $regexValue->accepts(123456);      // true - converts to string then matches
 *
 * // Invalid inputs
 * $regexValue->accepts('ab1234');    // false - lowercase letters
 * $regexValue->accepts('ABC123');    // false - 3 letters instead of 2
 * $regexValue->accepts('AB12');      // false - only 2 digits instead of 4
 * $regexValue->accepts('AB12CD');    // false - extra characters
 * $regexValue->accepts([]);          // false - not string/numeric
 * ```
 */
class RegexValue implements ValueInterface
{
    public function __construct(
        private readonly string $pattern,
    ) {}

    public function accepts(mixed $value): bool
    {
        return (\is_numeric($value) || \is_string($value)) && $this->isValid($this->convert($value));
    }

    public function convert(mixed $value): string
    {
        return (string) $value;
    }

    private function isValid(string $value): bool
    {
        return (bool) \preg_match($this->pattern, $value);
    }
}
