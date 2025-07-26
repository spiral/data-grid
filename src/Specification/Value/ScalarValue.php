<?php

declare(strict_types=1);

namespace Spiral\DataGrid\Specification\Value;

use Spiral\DataGrid\Specification\ValueInterface;

/**
 * Validates scalar values (string, int, float, bool) with optional empty string handling.
 * More permissive than StringValue - accepts any scalar type and converts to appropriate format.
 *
 * Real-world usage examples:
 * - Form input processing: Handle various HTML form input types
 * - API parameter validation: Accept different scalar parameter formats
 * - Configuration values: Settings that can be strings, numbers, or booleans
 * - Search input: Accept numeric or text search terms
 * - Filter values: Handle mixed scalar filter criteria
 * - Data import: Process CSV data with mixed scalar types
 * - User preferences: Settings that may be text, numbers, or flags
 * - Dynamic content: Handle varied content types from external sources
 *
 * Scalar types in PHP:
 * - string: 'hello', '123', ''
 * - int: 123, 0, -456
 * - float: 12.34, 0.0, -5.67
 * - bool: true, false
 *
 * @example
 * // Mixed form input processing
 * $formValue = new ScalarValue();
 * $formFilter = new Equals('user_input', $formValue);
 * $result = $formFilter->withValue('text input');  // Valid - string
 * $result = $formFilter->withValue(123);           // Valid - integer
 * $result = $formFilter->withValue(45.67);         // Valid - float
 * $result = $formFilter->withValue(true);          // Valid - boolean
 *
 * @example
 * // API parameter that accepts various types
 * $apiValue = new ScalarValue();
 * $apiFilter = new Equals('parameter', $apiValue);
 * $result = $apiFilter->withValue('string_param');  // String parameter
 * $result = $apiFilter->withValue(100);             // Numeric parameter
 * $result = $apiFilter->withValue(true);            // Boolean parameter
 *
 * @example
 * // Configuration setting (can be text, number, or boolean)
 * $configValue = new ScalarValue();
 * $settingFilter = new Equals('setting_value', $configValue);
 * $result = $settingFilter->withValue('debug');     // String setting
 * $result = $settingFilter->withValue(5);           // Numeric setting
 * $result = $settingFilter->withValue(false);       // Boolean setting
 *
 * @example
 * // Search input (accepts text or numbers)
 * $searchValue = new ScalarValue();
 * $searchFilter = new Like('search_term', $searchValue);
 * $result = $searchFilter->withValue('product name'); // Text search
 * $result = $searchFilter->withValue(12345);          // SKU/ID search
 *
 * @example
 * // Allow empty strings option
 * $allowEmptyValue = new ScalarValue(true);  // Allow empty strings
 * $strictValue = new ScalarValue(false);     // Reject empty strings (default)
 *
 * $allowEmptyValue->accepts('');     // true - empty allowed
 * $allowEmptyValue->accepts('text'); // true - non-empty string
 * $strictValue->accepts('');         // false - empty not allowed
 * $strictValue->accepts('text');     // true - non-empty string
 *
 * @example
 * // Data import processing (CSV with mixed types)
 * $csvValue = new ScalarValue(true); // Allow empty fields
 * $importFilter = new Equals('csv_field', $csvValue);
 * $result = $importFilter->withValue('text data');  // String field
 * $result = $importFilter->withValue(123.45);       // Numeric field
 * $result = $importFilter->withValue('');           // Empty field (allowed)
 *
 * @example
 * // User preference storage
 * $preferenceValue = new ScalarValue();
 * $userFilter = new Map([
 *     'theme' => new Equals('theme', $preferenceValue),        // 'dark', 'light'
 *     'font_size' => new Equals('font_size', $preferenceValue), // 12, 14, 16
 *     'notifications' => new Equals('notifications', $preferenceValue) // true, false
 * ]);
 *
 * @example
 * // Validation examples
 * $scalarValue = new ScalarValue(); // Default: empty strings not allowed
 *
 * // Valid inputs (scalar types)
 * $scalarValue->accepts('hello');    // true - string
 * $scalarValue->accepts('123');      // true - string
 * $scalarValue->accepts(123);        // true - integer
 * $scalarValue->accepts(12.3);       // true - float
 * $scalarValue->accepts(true);       // true - boolean
 * $scalarValue->accepts(false);      // true - boolean
 * $scalarValue->accepts(0);          // true - integer zero
 * $scalarValue->accepts(0.0);        // true - float zero
 *
 * // Invalid inputs (non-scalar or empty string by default)
 * $scalarValue->accepts('');         // false - empty string (default behavior)
 * $scalarValue->accepts([]);         // false - array
 * $scalarValue->accepts(null);       // false - null
 * $scalarValue->accepts(new \stdClass()); // false - object
 *
 * @example
 * // With empty strings allowed
 * $allowEmptyScalar = new ScalarValue(true);
 *
 * $allowEmptyScalar->accepts('');    // true - empty allowed
 * $allowEmptyScalar->accepts('text'); // true - string
 * $allowEmptyScalar->accepts(123);   // true - integer
 * $allowEmptyScalar->accepts([]);    // false - still not scalar
 *
 * @example
 * // Conversion examples (values returned as-is)
 * $scalarValue = new ScalarValue();
 *
 * $scalarValue->convert('hello');    // Returns 'hello' (string)
 * $scalarValue->convert(123);        // Returns 123 (integer)
 * $scalarValue->convert(45.67);      // Returns 45.67 (float)
 * $scalarValue->convert(true);       // Returns true (boolean)
 * $scalarValue->convert(false);      // Returns false (boolean)
 *
 * @example
 * // Dynamic filter processing
 * $dynamicValue = new ScalarValue();
 * $dynamicFilter = new Any(
 *     new Like('title', $dynamicValue),        // Text search
 *     new Equals('id', $dynamicValue),         // ID lookup
 *     new Equals('active', $dynamicValue)      // Boolean flag
 * );
 * // Accepts various search criteria types
 *
 * @example
 * // Form field validation
 * $fieldValue = new ScalarValue(true); // Allow empty for optional fields
 * if ($fieldValue->accepts($_POST['optional_field'])) {
 *     $validValue = $fieldValue->convert($_POST['optional_field']);
 *     // Process any scalar input or empty string
 * }
 *
 * @example
 * // API endpoint with flexible parameters
 * $flexibleParam = new ScalarValue();
 * // GET /api/search?q=text or GET /api/search?q=123 or GET /api/search?q=true
 * if ($flexibleParam->accepts($_GET['q'])) {
 *     $queryValue = $flexibleParam->convert($_GET['q']);
 *     // Handle string, numeric, or boolean search
 * }
 *
 * @example
 * // Configuration file processing
 * $configScalar = new ScalarValue(true);
 * $configFilter = new Map([
 *     'host' => new Equals('host', $configScalar),        // 'localhost'
 *     'port' => new Equals('port', $configScalar),        // 3306
 *     'debug' => new Equals('debug', $configScalar),      // true
 *     'timeout' => new Equals('timeout', $configScalar)   // 30.5
 * ]);
 *
 * @example
 * // Database field processing (mixed column types)
 * $dbValue = new ScalarValue(true); // Allow NULL/empty values
 * $recordFilter = new All(
 *     new Equals('name', $dbValue),           // String field
 *     new Gte('age', $dbValue),               // Integer field
 *     new Equals('salary', $dbValue),         // Float field
 *     new Equals('is_active', $dbValue)       // Boolean field
 * );
 *
 * @example
 * // JSON API processing
 * $jsonValue = new ScalarValue();
 * $jsonFilter = new Map([
 *     'title' => new Like('title', $jsonValue),
 *     'count' => new Gte('count', $jsonValue),
 *     'price' => new Between('price', $jsonValue),
 *     'featured' => new Equals('featured', $jsonValue)
 * ]);
 * // Handles mixed JSON property types
 *
 * @example
 * // Search system with type flexibility
 * $searchValue = new ScalarValue();
 * $searchSystem = new Select([
 *     'text' => new Like('content', $searchValue),      // Text search
 *     'id' => new Equals('id', $searchValue),           // ID search
 *     'price' => new Lte('price', $searchValue),        // Price search
 *     'active' => new Equals('is_active', $searchValue) // Status search
 * ]);
 *
 * @example
 * // Logging system (mixed log data)
 * $logValue = new ScalarValue(true); // Allow empty/null values
 * $logFilter = new All(
 *     new Equals('level', $logValue),      // 'info', 'error', etc.
 *     new Gte('timestamp', $logValue),     // Unix timestamp
 *     new Like('message', $logValue),      // Log message
 *     new Equals('user_id', $logValue)     // User ID or empty
 * );
 *
 * @example
 * // E-commerce product attributes
 * $attributeValue = new ScalarValue(true);
 * $productFilter = new Map([
 *     'color' => new Equals('color', $attributeValue),     // 'red', 'blue'
 *     'size' => new Equals('size', $attributeValue),       // 'M', 'L', 'XL'
 *     'weight' => new Gte('weight', $attributeValue),      // 1.5, 2.0
 *     'waterproof' => new Equals('waterproof', $attributeValue) // true, false
 * ]);
 *
 * @example
 * // Social media post processing
 * $postValue = new ScalarValue(true); // Allow empty optional fields
 * $socialFilter = new All(
 *     new Like('content', $postValue),         // Post text
 *     new Equals('likes', $postValue),         // Like count
 *     new Equals('is_public', $postValue),     // Visibility flag
 *     new Equals('location', $postValue)       // Location (optional)
 * );
 *
 * Important notes:
 * - Accepts any scalar type: string, int, float, bool
 * - Does not convert types - returns values as-is
 * - Empty string handling controlled by constructor parameter
 * - More permissive than StringValue, IntValue, etc.
 * - Useful for dynamic content with unknown types
 * - Arrays, objects, and null are always rejected
 * - Zero values (0, 0.0, false) are considered valid scalars
 * - Good for flexible APIs and mixed data processing
 */
final class ScalarValue implements ValueInterface
{
    public function __construct(
        private readonly bool $allowEmpty = false,
    ) {}

    public function accepts(mixed $value): bool
    {
        return \is_scalar($value) && ($this->allowEmpty || $value !== '');
    }

    public function convert(mixed $value): mixed
    {
        return $value;
    }
}
