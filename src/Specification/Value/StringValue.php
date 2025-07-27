<?php

declare(strict_types=1);

namespace Spiral\DataGrid\Specification\Value;

use Spiral\DataGrid\Specification\ValueInterface;

/**
 * Validates string and numeric input, converting to string format with optional empty string handling.
 * The most commonly used value type for text input processing.
 *
 * ```
 * // User name validation
 * $nameValue = new StringValue();
 * $userFilter = new Equals('name', $nameValue);
 * $result = $userFilter->withValue('John Doe');  // Valid string
 * $result = $userFilter->withValue(123);         // Valid - converts to '123'
 * $result = $userFilter->withValue('');          // Invalid - empty string (default)
 * ```
 * ```
 * // Search query processing
 * $searchValue = new StringValue();
 * $searchFilter = new Like('title', $searchValue);
 * $result = $searchFilter->withValue('PHP tutorial');  // Text search
 * $result = $searchFilter->withValue(12345);           // SKU search (converts to '12345')
 * ```
 * ```
 * // Allow empty strings (optional fields)
 * $optionalValue = new StringValue(true);  // Allow empty strings
 * $requiredValue = new StringValue(false); // Reject empty strings (default)
 *
 * $optionalValue->accepts('');      // true - empty allowed
 * $optionalValue->accepts('text');  // true - non-empty string
 * $requiredValue->accepts('');      // false - empty not allowed
 * $requiredValue->accepts('text');  // true - non-empty string
 * ```
 * ```
 * // Email address processing
 * $emailValue = new StringValue();
 * $emailFilter = new Like('email', $emailValue);
 * $result = $emailFilter->withValue('user@site.com'); // Valid email string
 * ```
 * ```
 * // Product description validation
 * $descriptionValue = new StringValue(true); // Allow empty descriptions
 * $productFilter = new Like('description', $descriptionValue);
 * $result = $productFilter->withValue('High-quality product'); // With description
 * $result = $productFilter->withValue('');                     // Empty allowed
 * ```
 * ```
 * // Comment system
 * $commentValue = new StringValue();
 * $commentFilter = new Like('comment_text', $commentValue);
 * $result = $commentFilter->withValue('Great article!'); // Valid comment
 * $result = $commentFilter->withValue('');               // Invalid - empty comment
 * ```
 * ```
 * // Tag processing
 * $tagValue = new StringValue();
 * $tagFilter = new InArray('tags', new ArrayValue($tagValue));
 * $result = $tagFilter->withValue(['php', 'programming', 'tutorial']); // String tags
 * ```
 * ```
 * // Validation examples
 * $stringValue = new StringValue(); // Default: empty strings not allowed
 *
 * // Valid inputs
 * $stringValue->accepts('hello');       // true - string
 * $stringValue->accepts('123');         // true - numeric string
 * $stringValue->accepts(123);           // true - converts to '123'
 * $stringValue->accepts(45.67);         // true - converts to '45.67'
 * $stringValue->accepts('0');           // true - string zero
 * $stringValue->accepts(0);             // true - converts to '0'
 *
 * // Invalid inputs (default behavior)
 * $stringValue->accepts('');            // false - empty string not allowed
 * $stringValue->accepts([]);            // false - array
 * $stringValue->accepts(null);          // false - null
 * $stringValue->accepts(true);          // false - boolean
 * $stringValue->accepts(false);         // false - boolean
 * ```
 */
final class StringValue implements ValueInterface
{
    public function __construct(
        private readonly bool $allowEmpty = false,
    ) {}

    public function accepts(mixed $value): bool
    {
        return (\is_numeric($value) || \is_string($value)) && ($this->allowEmpty || $this->convert($value) !== '');
    }

    public function convert(mixed $value): string
    {
        return (string) $value;
    }
}
