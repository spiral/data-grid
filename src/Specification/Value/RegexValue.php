<?php

declare(strict_types=1);

namespace Spiral\DataGrid\Specification\Value;

use Spiral\DataGrid\Specification\ValueInterface;

/**
 * Validates string or numeric values against a regular expression pattern.
 * Ensures input matches the specified regex pattern and converts to string format.
 *
 * Real-world usage examples:
 * - Email validation: Ensure email addresses match proper format
 * - Phone number validation: Validate phone number formats and patterns
 * - Password strength: Enforce password complexity requirements
 * - Format validation: SKU codes, license plates, postal codes, etc.
 * - Input sanitization: Allow only specific character patterns
 * - Data format enforcement: Date formats, time formats, custom patterns
 * - Security validation: Prevent malicious input patterns
 * - API parameter validation: Ensure parameters match expected formats
 *
 * Pattern examples:
 * - Email: '/^[^@]+@[^@]+\.[^@]+$/'
 * - Phone: '/^\+?[1-9]\d{1,14}$/'
 * - Password: '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/'
 * - UUID: '/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i'
 * - Alphanumeric: '/^[a-zA-Z0-9]+$/'
 *
 * @example
 * // Email validation
 * $emailValue = new RegexValue('/^[^@]+@[^@]+\.[^@]+$/');
 * $emailFilter = new Equals('email', $emailValue);
 * $result = $emailFilter->withValue('user@example.com'); // Valid email
 * $result = $emailFilter->withValue('invalid-email');    // Invalid - no @ or domain
 *
 * @example
 * // Phone number validation (international format)
 * $phoneValue = new RegexValue('/^\+?[1-9]\d{1,14}$/');
 * $contactFilter = new Equals('phone', $phoneValue);
 * $result = $contactFilter->withValue('+1234567890');  // Valid international format
 * $result = $contactFilter->withValue('123-456-7890'); // Invalid - contains dashes
 *
 * @example
 * // Password strength validation
 * $passwordValue = new RegexValue('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/');
 * $userFilter = new Equals('password', $passwordValue);
 * $result = $userFilter->withValue('StrongPass123'); // Valid - meets all requirements
 * $result = $userFilter->withValue('weakpass');      // Invalid - no uppercase/digits
 *
 * @example
 * // SKU/Product code validation
 * $skuValue = new RegexValue('/^[A-Z]{2,3}-\d{4,6}$/');
 * $productFilter = new Equals('sku', $skuValue);
 * $result = $productFilter->withValue('ABC-12345');  // Valid SKU format
 * $result = $productFilter->withValue('abc-123');    // Invalid - lowercase, too short
 *
 * @example
 * // Postal code validation (US ZIP codes)
 * $zipValue = new RegexValue('/^\d{5}(-\d{4})?$/');
 * $addressFilter = new Equals('zip_code', $zipValue);
 * $result = $zipFilter->withValue('12345');      // Valid - 5 digit ZIP
 * $result = $zipFilter->withValue('12345-6789'); // Valid - ZIP+4 format
 * $result = $zipFilter->withValue('ABC123');     // Invalid - contains letters
 *
 * @example
 * // License plate validation
 * $plateValue = new RegexValue('/^[A-Z0-9]{6,8}$/');
 * $vehicleFilter = new Equals('license_plate', $plateValue);
 * $result = $vehicleFilter->withValue('ABC1234');  // Valid plate
 * $result = $plateFilter->withValue('AB-123');     // Invalid - contains dash
 *
 * @example
 * // Alphanumeric username validation
 * $usernameValue = new RegexValue('/^[a-zA-Z0-9_]{3,20}$/');
 * $userFilter = new Equals('username', $usernameValue);
 * $result = $userFilter->withValue('user_123');     // Valid username
 * $result = $userFilter->withValue('user@domain');  // Invalid - contains @
 *
 * @example
 * // Date format validation (YYYY-MM-DD)
 * $dateValue = new RegexValue('/^\d{4}-\d{2}-\d{2}$/');
 * $eventFilter = new Equals('event_date', $dateValue);
 * $result = $eventFilter->withValue('2024-01-15');  // Valid date format
 * $result = $eventFilter->withValue('01/15/2024');  // Invalid - wrong format
 *
 * @example
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
 *
 * @example
 * // Conversion examples
 * $regexValue = new RegexValue('/^\d+$/'); // Only digits
 *
 * $regexValue->convert(12345);       // Returns '12345' (string)
 * $regexValue->convert('67890');     // Returns '67890' (string)
 * $regexValue->convert(123.45);      // Returns '123.45' (string, but won't match pattern)
 *
 * @example
 * // Credit card number validation (basic format)
 * $cardValue = new RegexValue('/^\d{4}-?\d{4}-?\d{4}-?\d{4}$/');
 * $paymentFilter = new Equals('card_number', $cardValue);
 * $result = $paymentFilter->withValue('1234-5678-9012-3456'); // Valid with dashes
 * $result = $paymentFilter->withValue('1234567890123456');     // Valid without dashes
 *
 * @example
 * // IPv4 address validation
 * $ipValue = new RegexValue('/^(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/');
 * $networkFilter = new Equals('ip_address', $ipValue);
 * $result = $networkFilter->withValue('192.168.1.1');  // Valid IP
 * $result = $networkFilter->withValue('256.1.1.1');    // Invalid - octet > 255
 *
 * @example
 * // Hexadecimal color validation
 * $colorValue = new RegexValue('/^#[0-9A-Fa-f]{6}$/');
 * $styleFilter = new Equals('color', $colorValue);
 * $result = $styleFilter->withValue('#FF5733');    // Valid hex color
 * $result = $styleFilter->withValue('#fff');       // Invalid - too short
 *
 * @example
 * // API key validation
 * $apiKeyValue = new RegexValue('/^[A-Za-z0-9]{32,64}$/');
 * $authFilter = new Equals('api_key', $apiKeyValue);
 * $result = $authFilter->withValue('abcd1234...'); // Valid if 32-64 chars
 *
 * @example
 * // Social Security Number validation (US format)
 * $ssnValue = new RegexValue('/^\d{3}-\d{2}-\d{4}$/');
 * $identityFilter = new Equals('ssn', $ssnValue);
 * $result = $identityFilter->withValue('123-45-6789'); // Valid SSN format
 * $result = $identityFilter->withValue('123456789');   // Invalid - no dashes
 *
 * @example
 * // Form validation
 * $phoneRegexValue = new RegexValue('/^\(\d{3}\) \d{3}-\d{4}$/');
 * if ($phoneRegexValue->accepts($_POST['phone'])) {
 *     $validPhone = $phoneRegexValue->convert($_POST['phone']);
 *     // Process valid phone number format
 * }
 *
 * @example
 * // API parameter validation
 * $tokenValue = new RegexValue('/^[A-Za-z0-9+\/]{40}={0,2}$/'); // Base64-like token
 * if ($tokenValue->accepts($_GET['token'])) {
 *     $validToken = $tokenValue->convert($_GET['token']);
 *     // Use validated token
 * }
 *
 * @example
 * // Multi-format validation with Select
 * $multiFormatSelect = new Select([
 *     'email' => new Equals('contact', new RegexValue('/^[^@]+@[^@]+\.[^@]+$/')),
 *     'phone' => new Equals('contact', new RegexValue('/^\+?[1-9]\d{1,14}$/')),
 *     'username' => new Equals('contact', new RegexValue('/^[a-zA-Z0-9_]{3,20}$/'))
 * ]);
 * // Accepts email, phone, or username formats
 *
 * @example
 * // Complex password requirements
 * $strongPasswordValue = new RegexValue('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{12,}$/');
 * // Requires: lowercase, uppercase, digit, special char, min 12 chars
 * $securityFilter = new Equals('password', $strongPasswordValue);
 *
 * @example
 * // File name validation
 * $fileNameValue = new RegexValue('/^[a-zA-Z0-9._-]+\.(jpg|jpeg|png|gif)$/i');
 * $uploadFilter = new Equals('filename', $fileNameValue);
 * $result = $uploadFilter->withValue('image.jpg');     // Valid image file
 * $result = $uploadFilter->withValue('document.pdf'); // Invalid - wrong extension
 *
 * @example
 * // Version number validation
 * $versionValue = new RegexValue('/^\d+\.\d+\.\d+$/');
 * $softwareFilter = new Equals('version', $versionValue);
 * $result = $softwareFilter->withValue('1.2.3');   // Valid semantic version
 * $result = $softwareFilter->withValue('v1.2.3');  // Invalid - extra 'v' prefix
 *
 * Important notes:
 * - Accepts string and numeric input (converts numeric to string)
 * - Always returns string after conversion
 * - Uses PHP's preg_match() function for pattern matching
 * - Invalid regex patterns may cause runtime errors
 * - Case sensitivity depends on regex flags (use /i for case-insensitive)
 * - Delimiter escaping required in patterns (use \/ for literal slash)
 * - Performance consideration: complex patterns may be slower
 * - Always validate patterns are working as expected in development
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
