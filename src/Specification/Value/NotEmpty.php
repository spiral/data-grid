<?php

declare(strict_types=1);

namespace Spiral\DataGrid\Specification\Value;

use Spiral\DataGrid\Specification\ValueInterface;

/**
 * Wrapper value that ensures input is not empty before passing to underlying ValueInterface.
 * Rejects empty values (empty arrays, empty strings, null, false, 0) and only accepts non-empty input.
 *
 * What counts as "empty":
 * - Empty strings: ''
 * - Empty arrays: []
 * - Null values: null
 * - Boolean false: false
 * - Zero values: 0, 0.0, '0'
 * - Whitespace-only strings (depending on underlying value type)
 *
 * ```
 * // Required search field
 * $searchValue = new NotEmpty(new StringValue());
 * $searchFilter = new Like('title', $searchValue);
 * $result = $searchFilter->withValue('php tutorial'); // Valid - non-empty search
 * $result = $searchFilter->withValue('');             // Invalid - empty search
 * ```
 * ```
 * // Required user name
 * $nameValue = new NotEmpty(new StringValue());
 * $userFilter = new Equals('username', $nameValue);
 * $result = $userFilter->withValue('john_doe');  // Valid - has username
 * $result = $userFilter->withValue('');          // Invalid - empty username
 * $result = $userFilter->withValue(null);        // Invalid - null username
 * ```
 * ```
 * // Required numeric input
 * $quantityValue = new NotEmpty(new IntValue());
 * $orderFilter = new Gte('quantity', $quantityValue);
 * $result = $orderFilter->withValue(5);   // Valid - quantity provided
 * $result = $orderFilter->withValue(0);   // Invalid - zero quantity (empty)
 * $result = $orderFilter->withValue('');  // Invalid - empty input
 * ```
 * ```
 * // Required array input (tags, categories, etc.)
 * $tagsValue = new NotEmpty(new ArrayValue(new StringValue()));
 * $contentFilter = new InArray('tags', $tagsValue);
 * $result = $contentFilter->withValue(['php', 'tutorial']); // Valid - has tags
 * $result = $contentFilter->withValue([]);                 // Invalid - empty array
 * ```
 * ```
 * // Required configuration value
 * $configValue = new NotEmpty(new StringValue());
 * $settingFilter = new Equals('api_key', $configValue);
 * $result = $settingFilter->withValue('abc123key'); // Valid - API key provided
 * $result = $settingFilter->withValue('');          // Invalid - empty key
 * ```
 * ```
 * // File upload validation
 * $fileValue = new NotEmpty(new StringValue());
 * $uploadFilter = new Equals('filename', $fileValue);
 * $result = $uploadFilter->withValue('document.pdf'); // Valid - file selected
 * $result = $uploadFilter->withValue('');             // Invalid - no file
 * ```
 * ```
 * // Comment validation
 * $commentValue = new NotEmpty(new StringValue());
 * $commentFilter = new Like('comment_text', $commentValue);
 * $result = $commentFilter->withValue('Great article!'); // Valid - has comment
 * $result = $commentFilter->withValue('');               // Invalid - empty comment
 * ```
 * ```
 * // Email validation (must not be empty AND valid format)
 * $emailValue = new NotEmpty(new RegexValue('/^[^@]+@[^@]+\.[^@]+$/'));
 * $userFilter = new Equals('email', $emailValue);
 * $result = $userFilter->withValue('user@example.com'); // Valid - non-empty valid email
 * $result = $userFilter->withValue('');                 // Invalid - empty email
 * $result = $userFilter->withValue('invalid');          // Invalid - non-empty but invalid format
 * ```
 * ```
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
 * ```
 * ```
 * // Without underlying value (accepts any non-empty)
 * $anyNotEmpty = new NotEmpty();
 * $anyNotEmpty->accepts('hello');    // true
 * $anyNotEmpty->accepts(123);        // true
 * $anyNotEmpty->accepts([1, 2]);     // true
 * $anyNotEmpty->accepts('');         // false
 * $anyNotEmpty->accepts(null);       // false
 * ```
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
