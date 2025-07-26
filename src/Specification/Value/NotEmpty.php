<?php

declare(strict_types=1);

namespace Spiral\DataGrid\Specification\Value;

use Spiral\DataGrid\Specification\ValueInterface;

/**
 * Wrapper value that ensures input is not empty before passing to underlying ValueInterface.
 * Rejects empty values (empty arrays, empty strings, null, false, 0) and only accepts non-empty input.
 *
 * Real-world usage examples:
 * - Required form fields: Ensure user provides actual input, not just empty strings
 * - Search validation: Prevent empty search queries from being processed
 * - API parameter validation: Ensure required parameters have meaningful values
 * - File upload validation: Ensure files are actually provided
 * - Text content validation: Prevent saving empty content or comments
 * - Configuration validation: Ensure settings have actual values
 * - Database field validation: Prevent empty required fields
 * - User input sanitization: Ensure meaningful user input before processing
 *
 * What counts as "empty":
 * - Empty strings: ''
 * - Empty arrays: []
 * - Null values: null
 * - Boolean false: false
 * - Zero values: 0, 0.0, '0'
 * - Whitespace-only strings (depending on underlying value type)
 *
 * @example
 * // Required search field
 * $searchValue = new NotEmpty(new StringValue());
 * $searchFilter = new Like('title', $searchValue);
 * $result = $searchFilter->withValue('php tutorial'); // Valid - non-empty search
 * $result = $searchFilter->withValue('');             // Invalid - empty search
 *
 * @example
 * // Required user name
 * $nameValue = new NotEmpty(new StringValue());
 * $userFilter = new Equals('username', $nameValue);
 * $result = $userFilter->withValue('john_doe');  // Valid - has username
 * $result = $userFilter->withValue('');          // Invalid - empty username
 * $result = $userFilter->withValue(null);        // Invalid - null username
 *
 * @example
 * // Required numeric input
 * $quantityValue = new NotEmpty(new IntValue());
 * $orderFilter = new Gte('quantity', $quantityValue);
 * $result = $orderFilter->withValue(5);   // Valid - quantity provided
 * $result = $orderFilter->withValue(0);   // Invalid - zero quantity (empty)
 * $result = $orderFilter->withValue('');  // Invalid - empty input
 *
 * @example
 * // Required array input (tags, categories, etc.)
 * $tagsValue = new NotEmpty(new ArrayValue(new StringValue()));
 * $contentFilter = new InArray('tags', $tagsValue);
 * $result = $contentFilter->withValue(['php', 'tutorial']); // Valid - has tags
 * $result = $contentFilter->withValue([]);                 // Invalid - empty array
 *
 * @example
 * // Required configuration value
 * $configValue = new NotEmpty(new StringValue());
 * $settingFilter = new Equals('api_key', $configValue);
 * $result = $settingFilter->withValue('abc123key'); // Valid - API key provided
 * $result = $settingFilter->withValue('');          // Invalid - empty key
 *
 * @example
 * // File upload validation
 * $fileValue = new NotEmpty(new StringValue());
 * $uploadFilter = new Equals('filename', $fileValue);
 * $result = $uploadFilter->withValue('document.pdf'); // Valid - file selected
 * $result = $uploadFilter->withValue('');             // Invalid - no file
 *
 * @example
 * // Comment validation
 * $commentValue = new NotEmpty(new StringValue());
 * $commentFilter = new Like('comment_text', $commentValue);
 * $result = $commentFilter->withValue('Great article!'); // Valid - has comment
 * $result = $commentFilter->withValue('');               // Invalid - empty comment
 *
 * @example
 * // Email validation (must not be empty AND valid format)
 * $emailValue = new NotEmpty(new RegexValue('/^[^@]+@[^@]+\.[^@]+$/'));
 * $userFilter = new Equals('email', $emailValue);
 * $result = $userFilter->withValue('user@example.com'); // Valid - non-empty valid email
 * $result = $userFilter->withValue('');                 // Invalid - empty email
 * $result = $userFilter->withValue('invalid');          // Invalid - non-empty but invalid format
 *
 * @example
 * // Validation examples
 * $notEmptyValue = new NotEmpty(new StringValue());
 *
 * // Valid inputs (non-empty)
 * $notEmptyValue->accepts('hello');        // true - non-empty string
 * $notEmptyValue->accepts('0');            // depends on underlying StringValue rules
 * $notEmptyValue->accepts(123);            // true - non-empty number
 * $notEmptyValue->accepts('   text   ');   // true - has content (whitespace handling varies)
 *
 * // Invalid inputs (empty)
 * $notEmptyValue->accepts('');             // false - empty string
 * $notEmptyValue->accepts(null);           // false - null
 * $notEmptyValue->accepts(false);          // false - boolean false
 * $notEmptyValue->accepts([]);             // false - empty array
 * $notEmptyValue->accepts(0);              // false - zero
 *
 * @example
 * // Without underlying value (accepts any non-empty)
 * $anyNotEmpty = new NotEmpty();
 * $anyNotEmpty->accepts('hello');    // true
 * $anyNotEmpty->accepts(123);        // true
 * $anyNotEmpty->accepts([1, 2]);     // true
 * $anyNotEmpty->accepts('');         // false
 * $anyNotEmpty->accepts(null);       // false
 *
 * @example
 * // Conversion examples
 * $notEmptyString = new NotEmpty(new StringValue());
 * $notEmptyString->convert('hello');  // Returns 'hello' (string conversion)
 * $notEmptyString->convert(123);      // Returns '123' (converted to string)
 *
 * $notEmptyInt = new NotEmpty(new IntValue());
 * $notEmptyInt->convert('123');       // Returns 123 (converted to int)
 * $notEmptyInt->convert(45.7);        // Returns 45 (converted to int)
 *
 * @example
 * // API endpoint validation
 * $requiredParam = new NotEmpty(new StringValue());
 * if ($requiredParam->accepts($_GET['query'])) {
 *     $validQuery = $requiredParam->convert($_GET['query']);
 *     // Process non-empty search query
 * } else {
 *     // Return error: required parameter is empty
 * }
 *
 * @example
 * // Form validation
 * $requiredName = new NotEmpty(new StringValue());
 * $requiredEmail = new NotEmpty(new RegexValue('/^[^@]+@[^@]+\.[^@]+$/'));
 *
 * if ($requiredName->accepts($_POST['name']) &&
 *     $requiredEmail->accepts($_POST['email'])) {
 *     // Process valid form data
 * } else {
 *     // Show validation errors
 * }
 *
 * @example
 * // Database insertion validation
 * $requiredTitle = new NotEmpty(new StringValue());
 * $requiredContent = new NotEmpty(new StringValue());
 *
 * $articleFilter = new All(
 *     new Equals('title', $requiredTitle),
 *     new Like('content', $requiredContent)
 * );
 * // Ensures both title and content are provided
 *
 * @example
 * // Configuration file validation
 * $requiredSetting = new NotEmpty(new StringValue());
 * $configFilter = new Map([
 *     'database_host' => new Equals('host', $requiredSetting),
 *     'database_name' => new Equals('name', $requiredSetting),
 *     'api_key' => new Equals('key', $requiredSetting)
 * ]);
 * // Ensures all required config values are provided
 *
 * @example
 * // Multi-language content validation
 * $requiredContent = new NotEmpty(new StringValue());
 * $contentFilter = new Map([
 *     'title_en' => new Equals('title_en', $requiredContent),
 *     'title_es' => new Equals('title_es', $requiredContent),
 *     'content_en' => new Like('content_en', $requiredContent)
 * ]);
 * // Ensures content is provided in required languages
 *
 * @example
 * // E-commerce product validation
 * $requiredField = new NotEmpty(new StringValue());
 * $requiredPrice = new NotEmpty(new PositiveValue(new NumericValue()));
 *
 * $productFilter = new All(
 *     new Equals('product_name', $requiredField),
 *     new Equals('description', $requiredField),
 *     new Gte('price', $requiredPrice)
 * );
 * // Ensures product has name, description, and positive price
 *
 * @example
 * // User registration validation
 * $requiredUsername = new NotEmpty(new StringValue());
 * $requiredPassword = new NotEmpty(new StringValue());
 * $requiredEmail = new NotEmpty(new RegexValue('/^[^@]+@[^@]+\.[^@]+$/'));
 *
 * $registrationFilter = new All(
 *     new Equals('username', $requiredUsername),
 *     new Equals('password', $requiredPassword),
 *     new Equals('email', $requiredEmail)
 * );
 * // Ensures all required registration fields are provided
 *
 * Important notes:
 * - Empty values are checked before underlying value validation
 * - If no underlying ValueInterface provided, accepts any non-empty value
 * - Underlying value conversion is still applied to non-empty values
 * - Useful for making any value type "required"
 * - Zero (0) is considered empty by PHP's empty() function
 * - Whitespace-only strings may or may not be empty depending on underlying value
 * - Combines well with other value types for comprehensive validation
 */
final class NotEmpty implements ValueInterface
{
    public function __construct(
        private readonly ?ValueInterface $value = null,
    ) {}

    public function accepts(mixed $value): bool
    {
        return match (true) {
            empty($value) => false,
            $this->value instanceof ValueInterface => $this->value->accepts($value),
            default => true,
        };
    }

    public function convert(mixed $value): mixed
    {
        if ($this->value instanceof ValueInterface) {
            return $this->value->convert($value);
        }

        return $value;
    }
}
