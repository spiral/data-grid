<?php

declare(strict_types=1);

namespace Spiral\DataGrid\Specification\Value;

use Spiral\DataGrid\Specification\ValueInterface;

/**
 * Validates string and numeric input, converting to string format with optional empty string handling.
 * The most commonly used value type for text input processing.
 *
 * Real-world usage examples:
 * - Text input validation: User names, descriptions, comments, search terms
 * - Form processing: HTML form text inputs, textareas, search boxes
 * - API parameters: String parameters that may come as numbers
 * - Content management: Article titles, post content, tag names
 * - Search functionality: Search queries, filter terms, keywords
 * - Database text fields: VARCHAR, TEXT column validation
 * - User-generated content: Reviews, comments, descriptions
 * - Configuration values: Settings that should be stored as strings
 *
 * Input flexibility:
 * - Accepts strings: 'hello', 'user@example.com', ''
 * - Accepts numbers: 123 → '123', 45.67 → '45.67'
 * - Converts to string format for consistent handling
 *
 * @example
 * // User name validation
 * $nameValue = new StringValue();
 * $userFilter = new Equals('name', $nameValue);
 * $result = $userFilter->withValue('John Doe');  // Valid string
 * $result = $userFilter->withValue(123);         // Valid - converts to '123'
 * $result = $userFilter->withValue('');          // Invalid - empty string (default)
 *
 * @example
 * // Search query processing
 * $searchValue = new StringValue();
 * $searchFilter = new Like('title', $searchValue);
 * $result = $searchFilter->withValue('PHP tutorial');  // Text search
 * $result = $searchFilter->withValue(12345);           // SKU search (converts to '12345')
 *
 * @example
 * // Allow empty strings (optional fields)
 * $optionalValue = new StringValue(true);  // Allow empty strings
 * $requiredValue = new StringValue(false); // Reject empty strings (default)
 *
 * $optionalValue->accepts('');      // true - empty allowed
 * $optionalValue->accepts('text');  // true - non-empty string
 * $requiredValue->accepts('');      // false - empty not allowed
 * $requiredValue->accepts('text');  // true - non-empty string
 *
 * @example
 * // Email address processing
 * $emailValue = new StringValue();
 * $emailFilter = new Like('email', $emailValue);
 * $result = $emailFilter->withValue('user@example.com'); // Valid email string
 *
 * @example
 * // Product description validation
 * $descriptionValue = new StringValue(true); // Allow empty descriptions
 * $productFilter = new Like('description', $descriptionValue);
 * $result = $productFilter->withValue('High-quality product'); // With description
 * $result = $productFilter->withValue('');                     // Empty allowed
 *
 * @example
 * // Comment system
 * $commentValue = new StringValue();
 * $commentFilter = new Like('comment_text', $commentValue);
 * $result = $commentFilter->withValue('Great article!'); // Valid comment
 * $result = $commentFilter->withValue('');               // Invalid - empty comment
 *
 * @example
 * // Tag processing
 * $tagValue = new StringValue();
 * $tagFilter = new InArray('tags', new ArrayValue($tagValue));
 * $result = $tagFilter->withValue(['php', 'programming', 'tutorial']); // String tags
 *
 * @example
 * // Validation examples
 * $stringValue = new StringValue(); // Default: empty strings not allowed
 *
 * // Valid inputs
 * $stringValue->accepts('hello');       // true - string
 * $stringValue->accepts('user@site');   // true - string with special chars
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
 *
 * @example
 * // With empty strings allowed
 * $allowEmptyString = new StringValue(true);
 *
 * $allowEmptyString->accepts('');       // true - empty allowed
 * $allowEmptyString->accepts('text');   // true - string
 * $allowEmptyString->accepts(123);      // true - converts to '123'
 * $allowEmptyString->accepts([]);       // false - still not string/numeric
 *
 * @example
 * // Conversion examples
 * $stringValue = new StringValue();
 *
 * $stringValue->convert('hello');       // Returns 'hello'
 * $stringValue->convert(123);           // Returns '123'
 * $stringValue->convert(45.67);         // Returns '45.67'
 * $stringValue->convert(0);             // Returns '0'
 * $stringValue->convert('0');           // Returns '0'
 *
 * @example
 * // Form processing
 * $formStringValue = new StringValue(true); // Optional field
 * if ($formStringValue->accepts($_POST['bio'])) {
 *     $userBio = $formStringValue->convert($_POST['bio']);
 *     // Process user biography (can be empty)
 * }
 *
 * @example
 * // API parameter validation
 * $apiStringValue = new StringValue();
 * // GET /api/search?q=search+term
 * if ($apiStringValue->accepts($_GET['q'])) {
 *     $searchQuery = $apiStringValue->convert($_GET['q']);
 *     // Process search query string
 * }
 *
 * @example
 * // Content management
 * $titleValue = new StringValue();        // Required title
 * $excerptValue = new StringValue(true);  // Optional excerpt
 *
 * $contentFilter = new All(
 *     new Like('title', $titleValue),      // Title required
 *     new Like('excerpt', $excerptValue)   // Excerpt optional
 * );
 *
 * @example
 * // Database text field processing
 * $textFieldValue = new StringValue(true); // Allow empty for optional fields
 * $recordFilter = new Map([
 *     'first_name' => new Equals('first_name', new StringValue()),     // Required
 *     'middle_name' => new Equals('middle_name', $textFieldValue),     // Optional
 *     'last_name' => new Equals('last_name', new StringValue()),       // Required
 *     'notes' => new Like('notes', $textFieldValue)                    // Optional
 * ]);
 *
 * @example
 * // Social media post processing
 * $postContentValue = new StringValue();
 * $hashtagValue = new StringValue();
 *
 * $socialFilter = new All(
 *     new Like('post_content', $postContentValue),
 *     new InArray('hashtags', new ArrayValue($hashtagValue))
 * );
 *
 * @example
 * // E-commerce product data
 * $productStringValue = new StringValue(true); // Some fields optional
 * $productFilter = new Map([
 *     'name' => new Like('name', new StringValue()),           // Required
 *     'description' => new Like('description', $productStringValue), // Optional
 *     'sku' => new Equals('sku', new StringValue()),           // Required
 *     'brand' => new Equals('brand', $productStringValue)      // Optional
 * ]);
 *
 * @example
 * // Search system with string processing
 * $searchStringValue = new StringValue();
 * $searchFilter = new Any(
 *     new Like('title', $searchStringValue),
 *     new Like('description', $searchStringValue),
 *     new Like('tags', $searchStringValue)
 * );
 * // Search across multiple text fields
 *
 * @example
 * // User registration validation
 * $requiredString = new StringValue();        // Required fields
 * $optionalString = new StringValue(true);    // Optional fields
 *
 * $registrationFilter = new All(
 *     new Equals('username', $requiredString),     // Username required
 *     new Equals('email', $requiredString),        // Email required
 *     new Equals('bio', $optionalString),          // Bio optional
 *     new Equals('website', $optionalString)       // Website optional
 * );
 *
 * @example
 * // Configuration file processing
 * $configStringValue = new StringValue(true); // Allow empty config values
 * $configFilter = new Map([
 *     'app_name' => new Equals('app_name', new StringValue()),    // Required
 *     'app_description' => new Equals('description', $configStringValue), // Optional
 *     'admin_email' => new Equals('admin_email', new StringValue()), // Required
 *     'support_email' => new Equals('support_email', $configStringValue) // Optional
 * ]);
 *
 * @example
 * // Blog system
 * $blogStringValue = new StringValue(true);
 * $blogFilter = new Map([
 *     'title' => new Like('title', new StringValue()),           // Required
 *     'content' => new Like('content', new StringValue()),       // Required
 *     'excerpt' => new Like('excerpt', $blogStringValue),        // Optional
 *     'meta_description' => new Equals('meta_desc', $blogStringValue) // Optional
 * ]);
 *
 * @example
 * // Address validation
 * $addressStringValue = new StringValue(true); // Some address parts optional
 * $addressFilter = new Map([
 *     'street' => new Like('street', new StringValue()),         // Required
 *     'city' => new Equals('city', new StringValue()),           // Required
 *     'state' => new Equals('state', new StringValue()),         // Required
 *     'apartment' => new Equals('apartment', $addressStringValue), // Optional
 *     'company' => new Equals('company', $addressStringValue)    // Optional
 * ]);
 *
 * @example
 * // Event management
 * $eventStringValue = new StringValue(true);
 * $eventFilter = new All(
 *     new Like('event_name', new StringValue()),                 // Required
 *     new Like('description', $eventStringValue),               // Optional
 *     new Equals('location', new StringValue()),                // Required
 *     new Like('special_instructions', $eventStringValue)       // Optional
 * );
 *
 * @example
 * // Customer feedback system
 * $feedbackStringValue = new StringValue(true);
 * $feedbackFilter = new Map([
 *     'customer_name' => new Equals('name', $feedbackStringValue),     // Optional
 *     'email' => new Equals('email', $feedbackStringValue),            // Optional
 *     'feedback' => new Like('feedback', new StringValue()),           // Required
 *     'suggestions' => new Like('suggestions', $feedbackStringValue)   // Optional
 * ]);
 *
 * Important notes:
 * - Accepts string and numeric input (converts numbers to strings)
 * - Always returns string after conversion
 * - Empty string handling controlled by constructor parameter (default: not allowed)
 * - Most commonly used value type for text processing
 * - Numeric inputs automatically converted to string representation
 * - Boolean values are rejected (use ScalarValue if needed)
 * - Arrays and objects are always rejected
 * - Perfect for user input, search terms, and text content
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
