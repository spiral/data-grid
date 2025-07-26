<?php

declare(strict_types=1);

namespace Spiral\DataGrid\Specification\Value\Accessor;

/**
 * String trimming accessor that removes whitespace from the beginning and end of strings.
 * Essential for cleaning user input and ensuring consistent data processing.
 *
 * Real-world usage examples:
 * - Form input cleaning: Remove accidental whitespace from user form submissions
 * - Search query processing: Clean search terms for better matching
 * - Database preparation: Ensure clean data before storage
 * - API parameter cleaning: Remove whitespace from request parameters
 * - CSV data processing: Clean imported data with extra spaces
 * - User registration: Clean usernames, emails, names before validation
 * - Content management: Clean article titles, descriptions, tags
 * - Configuration processing: Clean config values and settings
 *
 * Whitespace characters removed:
 * - Spaces: ' '
 * - Tabs: '\t'
 * - Newlines: '\n', '\r'
 * - Vertical tabs: '\v'
 * - Form feeds: '\f'
 * - Null bytes: '\0'
 *
 * Benefits:
 * - Prevents validation errors due to hidden whitespace
 * - Improves data quality and consistency
 * - Better user experience (users don't need to worry about spaces)
 * - Reduces duplicate entries caused by whitespace differences
 * - Essential preprocessing for most string operations
 *
 * @example
 * // Basic whitespace trimming
 * $trimmedString = new Trim(new StringValue());
 * $userFilter = new Equals('username', $trimmedString);
 * $result = $userFilter->withValue('  john_doe  '); // Converts to 'john_doe'
 *
 * @example
 * // Email address cleaning
 * $emailProcessor = new Trim(
 *     new ToLower(
 *         new RegexValue('/^[^@]+@[^@]+\.[^@]+$/')
 *     )
 * );
 * $emailFilter = new Equals('email', $emailProcessor);
 * $result = $emailFilter->withValue('  USER@EXAMPLE.COM  ');
 * // Results in: 'user@example.com' (trimmed and lowercased)
 *
 * @example
 * // Search query cleaning
 * $searchProcessor = new Trim(
 *     new StringValue()
 * );
 * $searchFilter = new Like('content', $searchProcessor);
 * $result = $searchFilter->withValue('  search term  '); // Clean search: 'search term'
 *
 * @example
 * // Form field processing
 * $nameProcessor = new Trim(new StringValue());
 * $formFilter = new Map([
 *     'first_name' => new Equals('first_name', $nameProcessor),
 *     'last_name' => new Equals('last_name', $nameProcessor),
 *     'company' => new Equals('company', new Trim(new StringValue(true))) // Optional field
 * ]);
 * // Cleans all name fields: '  John  ' -> 'John'
 *
 * @example
 * // CSV data cleaning
 * $csvProcessor = new Split(
 *     new ArrayValue(
 *         new Trim(new StringValue())
 *     ),
 *     ','
 * );
 * $csvFilter = new InArray('csv_row', $csvProcessor);
 * $result = $csvFilter->withValue('  John Doe  ,  30  ,  Engineer  ');
 * // Results in: ['John Doe', '30', 'Engineer'] (all fields trimmed)
 *
 * @example
 * // Tag processing with trimming
 * $tagProcessor = new Split(
 *     new ArrayValue(
 *         new Trim(
 *             new ToLower(new StringValue())
 *         )
 *     ),
 *     ','
 * );
 * $tagFilter = new InArray('tags', $tagProcessor);
 * $result = $tagFilter->withValue('  PHP  ,  JavaScript  ,  Python  ');
 * // Results in: ['php', 'javascript', 'python'] (trimmed and lowercased)
 *
 * @example
 * // Configuration value cleaning
 * $configProcessor = new Trim(
 *     new EnumValue(new StringValue(), 'debug', 'info', 'warning', 'error')
 * );
 * $configFilter = new Equals('log_level', $configProcessor);
 * $result = $configFilter->withValue('  debug  '); // Converts to 'debug'
 *
 * @example
 * // User registration processing
 * $registrationProcessor = new Trim(new StringValue());
 * $userFilter = new All(
 *     new Equals('username', $registrationProcessor),
 *     new Equals('email', new Trim(new RegexValue('/^[^@]+@[^@]+\.[^@]+$/'))),
 *     new Like('full_name', $registrationProcessor)
 * );
 * // Ensures all user input is properly trimmed
 *
 * @example
 * // API parameter cleaning
 * $apiProcessor = new Trim(new StringValue());
 * // GET /api/users?name=  John  Doe  
 * if ($apiProcessor->accepts($_GET['name'])) {
 *     $cleanName = $apiProcessor->convert($_GET['name']);
 *     // Results in: 'John  Doe' (outer spaces removed, inner preserved)
 * }
 *
 * @example
 * // Validation examples
 * $trim = new Trim(new StringValue());
 * 
 * // Valid inputs (strings that can be trimmed)
 * $trim->accepts('  hello  ');      // true - string with spaces
 * $trim->accepts('\t\ntext\r\n');   // true - string with various whitespace
 * $trim->accepts('already-clean');  // true - string without extra whitespace
 * $trim->accepts('');               // depends on StringValue config
 * 
 * // Inputs handled by next in chain
 * $trim->accepts(123);              // true - StringValue can convert
 * $trim->accepts([]);               // false - StringValue can't handle arrays
 * $trim->accepts(null);             // false - StringValue can't handle null
 *
 * @example
 * // Conversion examples
 * $trim = new Trim(new StringValue());
 * 
 * $trim->convert('  hello  ');      // Returns 'hello'
 * $trim->convert('\t\nworld\r\n');  // Returns 'world'
 * $trim->convert('   ');            // Returns '' (empty after trimming)
 * $trim->convert('no-spaces');      // Returns 'no-spaces'
 * $trim->convert('  a  b  ');       // Returns 'a  b' (inner spaces preserved)
 *
 * @example
 * // Database insertion preparation
 * $dbProcessor = new Trim(new StringValue(true)); // Allow empty after trimming
 * $dbFilter = new Map([
 *     'title' => new Equals('title', $dbProcessor),
 *     'description' => new Like('description', $dbProcessor),
 *     'notes' => new Like('notes', $dbProcessor)
 * ]);
 * // Ensures all text fields are clean before database storage
 *
 * @example
 * // Content management system
 * $cmsProcessor = new Trim(new StringValue());
 * $cmsFilter = new All(
 *     new Like('article_title', $cmsProcessor),
 *     new Like('article_content', $cmsProcessor),
 *     new InArray('categories', new ArrayValue($cmsProcessor))
 * );
 * // Cleans all content fields
 *
 * @example
 * // Import data processing
 * $importProcessor = new Trim(
 *     new StringValue(true) // Allow empty values after trimming
 * );
 * $importFilter = new InArray('imported_data', new ArrayValue($importProcessor));
 * // Cleans imported data: '  value  ' -> 'value', '   ' -> ''
 *
 * @example
 * // Address form processing
 * $addressProcessor = new Trim(new StringValue());
 * $addressFilter = new Map([
 *     'street' => new Like('street', $addressProcessor),
 *     'city' => new Equals('city', $addressProcessor),
 *     'state' => new Equals('state', $addressProcessor),
 *     'zip' => new Equals('zip', $addressProcessor)
 * ]);
 * // Cleans all address components
 *
 * @example
 * // Social media handle processing
 * $handleProcessor = new Trim(
 *     new ToLower(
 *         new RegexValue('/^@[a-z0-9_]+$/')
 *     )
 * );
 * $socialFilter = new Equals('twitter_handle', $handleProcessor);
 * $result = $socialFilter->withValue('  @JohnDoe  '); // Becomes '@johndoe'
 *
 * @example
 * // Multi-line text processing
 * $textProcessor = new Trim(new StringValue());
 * $textFilter = new Like('comment', $textProcessor);
 * $result = $textFilter->withValue("\n\n  Great article!  \n\t");
 * // Results in: 'Great article!' (all surrounding whitespace removed)
 *
 * @example
 * // Phone number preprocessing
 * $phoneProcessor = new Trim(
 *     new RegexValue('/^\+?[1-9]\d{1,14}$/')
 * );
 * $phoneFilter = new Equals('phone', $phoneProcessor);
 * $result = $phoneFilter->withValue('  +1234567890  '); // Becomes '+1234567890'
 *
 * @example
 * // URL processing
 * $urlProcessor = new Trim(
 *     new RegexValue('/^https?:\/\/.+$/')
 * );
 * $urlFilter = new Equals('website', $urlProcessor);
 * $result = $urlFilter->withValue('  https://example.com  '); // Clean URL
 *
 * @example
 * // Product code cleaning
 * $codeProcessor = new Trim(
 *     new ToUpper(
 *         new RegexValue('/^[A-Z0-9-]+$/')
 *     )
 * );
 * $productFilter = new Equals('product_code', $codeProcessor);
 * $result = $productFilter->withValue('  abc-123  '); // Becomes 'ABC-123'
 *
 * @example
 * // Password field processing (trim but preserve internal spaces)
 * $passwordProcessor = new Trim(new StringValue());
 * $authFilter = new Equals('password', $passwordProcessor);
 * $result = $authFilter->withValue('  my pass word  '); // Becomes 'my pass word'
 *
 * @example
 * // File name processing
 * $fileProcessor = new Trim(
 *     new RegexValue('/^[a-zA-Z0-9._-]+$/')
 * );
 * $fileFilter = new Equals('filename', $fileProcessor);
 * $result = $fileFilter->withValue('  document.pdf  '); // Becomes 'document.pdf'
 *
 * @example
 * // Comment system processing
 * $commentProcessor = new Trim(new StringValue());
 * $commentFilter = new Like('user_comment', $commentProcessor);
 * $result = $commentFilter->withValue("\t  This is a great post!  \n");
 * // Results in: 'This is a great post!'
 *
 * @example
 * // Configuration file processing
 * $configProcessor = new Trim(new StringValue(true));
 * $configFilter = new Map([
 *     'database_host' => new Equals('host', $configProcessor),
 *     'database_port' => new Equals('port', $configProcessor),
 *     'api_key' => new Equals('key', $configProcessor)
 * ]);
 * // Cleans all config values
 *
 * @example
 * // Whitespace-only string handling
 * $strictProcessor = new Trim(new StringValue(false)); // Don't allow empty
 * $lenientProcessor = new Trim(new StringValue(true));  // Allow empty
 * 
 * $strictProcessor->accepts('   ');    // false - becomes empty after trim
 * $lenientProcessor->accepts('   ');   // true - empty allowed
 *
 * @example
 * // Complex processing chain
 * $complexProcessor = new Split(
 *     new ArrayValue(
 *         new ToLower(
 *             new Trim(
 *                 new EnumValue(new StringValue(), 'admin', 'user', 'guest')
 *             )
 *         )
 *     ),
 *     ','
 * );
 * $result = $complexProcessor->convert('  ADMIN  ,  User  ,  GUEST  ');
 * // Results in: ['admin', 'user', 'guest'] (split, trimmed, lowercased, validated)
 *
 * @example
 * // Data export preparation
 * $exportProcessor = new Trim(new StringValue(true));
 * $exportFilter = new Map([
 *     'name' => new Equals('name', $exportProcessor),
 *     'description' => new Like('description', $exportProcessor),
 *     'category' => new Equals('category', $exportProcessor)
 * ]);
 * // Ensures clean data for export
 *
 * @example
 * // Error handling
 * $safeProcessor = new Trim(new StringValue());
 * if ($safeProcessor->accepts($userInput)) {
 *     $cleanValue = $safeProcessor->convert($userInput);
 *     // Process cleaned string value
 * } else {
 *     // Handle non-string input
 * }
 *
 * @example
 * // Log message processing
 * $logProcessor = new Trim(new StringValue());
 * $logFilter = new Like('log_message', $logProcessor);
 * $result = $logFilter->withValue("  \n Error: Connection failed  \t  ");
 * // Results in: 'Error: Connection failed'
 *
 * @example
 * // Metadata processing
 * $metaProcessor = new Trim(new StringValue(true)); // Allow empty meta fields
 * $metaFilter = new Map([
 *     'title' => new Equals('meta_title', $metaProcessor),
 *     'description' => new Like('meta_description', $metaProcessor),
 *     'keywords' => new Split(new ArrayValue($metaProcessor), ',')
 * ]);
 *
 * @example
 * // User preference processing
 * $preferenceProcessor = new Trim(new StringValue());
 * $prefFilter = new All(
 *     new Equals('theme', $preferenceProcessor),        // '  dark  ' -> 'dark'
 *     new Equals('language', $preferenceProcessor),     // '  en  ' -> 'en'
 *     new Equals('timezone', $preferenceProcessor)      // '  UTC  ' -> 'UTC'
 * );
 *
 * Important notes:
 * - Only processes string values directly
 * - Uses PHP's trim() function (removes whitespace from both ends)
 * - Does not affect whitespace in the middle of strings
 * - Essential first step in most string processing chains
 * - Can reveal empty strings hidden by whitespace
 * - Should be used before validation to ensure clean input
 * - Particularly important for user-generated content
 * - Helps prevent database storage of unwanted whitespace
 * - Critical for form processing and data imports
 */
final class Trim extends Accessor
{
    protected function acceptsCurrent(mixed $value): bool
    {
        return \is_string($value);
    }

    protected function convertCurrent(mixed $value): mixed
    {
        return \is_string($value) ? \trim($value) : $value;
    }
}
