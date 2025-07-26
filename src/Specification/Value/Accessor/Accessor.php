<?php

declare(strict_types=1);

namespace Spiral\DataGrid\Specification\Value\Accessor;

use Spiral\DataGrid\Specification\ValueInterface;

/**
 * Abstract base class for value accessors that transform input before passing to underlying ValueInterface.
 * Accessors act as middleware for value processing, allowing you to apply transformations like trimming,
 * case conversion, splitting, or other modifications before the actual value validation occurs.
 *
 * Real-world usage examples:
 * - Text preprocessing: Trim whitespace, normalize case before validation
 * - Data normalization: Convert formats, split strings, clean input
 * - User input sanitization: Remove unwanted characters, standardize formatting
 * - API parameter processing: Transform request data into expected formats
 * - Search optimization: Normalize search terms for better matching
 * - Data import: Clean and format data from external sources
 * - Form processing: Standardize user input before storage
 * - Content management: Prepare content for consistent display
 *
 * Execution order:
 * 1. Current accessor processes the value (acceptsCurrent/convertCurrent)
 * 2. Result is passed to the next ValueInterface in the chain
 * 3. Chain continues until reaching the base value type
 *
 * Chaining pattern:
 * - Multiple accessors can be chained together
 * - Each accessor processes input, then passes result to next
 * - Final ValueInterface performs actual type validation
 *
 * @example
 * // Single accessor: trim whitespace before string validation
 * $trimmedString = new Trim(new StringValue());
 * $result = $trimmedString->convert('  hello world  '); // Returns 'hello world'
 *
 * @example
 * // Chained accessors: trim, then convert to uppercase
 * $cleanedString = new ToUpper(new Trim(new StringValue()));
 * $result = $cleanedString->convert('  hello world  '); // Returns 'HELLO WORLD'
 *
 * @example
 * // Complex chain: trim, lowercase, then validate against enum
 * $normalizedEnum = new ToLower(
 *     new Trim(
 *         new EnumValue(new StringValue(), 'admin', 'user', 'guest')
 *     )
 * );
 * $result = $normalizedEnum->convert('  ADMIN  '); // Returns 'admin'
 *
 * @example
 * // Split string into array, then validate each element
 * $tagList = new Split(
 *     new ArrayValue(new StringValue()),
 *     ','
 * );
 * $result = $tagList->convert('php,javascript,python'); // Returns ['php', 'javascript', 'python']
 *
 * @example
 * // User input processing chain
 * $userInput = new ToLower(
 *     new Trim(
 *         new StringValue()
 *     )
 * );
 * $filter = new Like('username', $userInput);
 * // User types '  JohnDoe  ' -> becomes 'johndoe' for search
 *
 * @example
 * // Email normalization
 * $emailNormalizer = new ToLower(
 *     new Trim(
 *         new RegexValue('/^[^@]+@[^@]+\.[^@]+$/')
 *     )
 * );
 * $emailFilter = new Equals('email', $emailNormalizer);
 * // '  USER@EXAMPLE.COM  ' -> 'user@example.com'
 *
 * @example
 * // Search term processing
 * $searchProcessor = new ToLower(
 *     new Trim(
 *         new StringValue()
 *     )
 * );
 * $searchFilter = new Like('content', $searchProcessor);
 * // Normalizes search terms for consistent matching
 *
 * @example
 * // CSV data processing
 * $csvProcessor = new Trim(
 *     new StringValue(true) // Allow empty after trimming
 * );
 * $csvFilter = new Equals('csv_field', $csvProcessor);
 * // Cleans CSV data: '  value  ' -> 'value', '   ' -> ''
 *
 * @example
 * // Tag processing from comma-separated input
 * $tagProcessor = new Split(
 *     new ArrayValue(
 *         new Trim(new StringValue())
 *     ),
 *     ','
 * );
 * $tagFilter = new InArray('tags', $tagProcessor);
 * // 'php, javascript , python' -> ['php', 'javascript', 'python']
 *
 * @example
 * // Configuration value processing
 * $configProcessor = new ToUpper(
 *     new Trim(
 *         new EnumValue(new StringValue(), 'DEBUG', 'INFO', 'ERROR')
 *     )
 * );
 * $configFilter = new Equals('log_level', $configProcessor);
 * // 'debug' -> 'DEBUG', '  info  ' -> 'INFO'
 *
 * @example
 * // Form input standardization
 * $nameProcessor = new Trim(new StringValue());
 * $phoneProcessor = new Trim(new RegexValue('/^\+?[1-9]\d{1,14}$/'));
 * $formFilter = new Map([
 *     'name' => new Equals('name', $nameProcessor),
 *     'phone' => new Equals('phone', $phoneProcessor)
 * ]);
 *
 * @example
 * // Acceptance logic examples
 * $processor = new ToUpper(new StringValue());
 *
 * // Accepts if current accessor can handle it OR next in chain can
 * $processor->accepts('hello');    // true - string (current can handle)
 * $processor->accepts(123);        // true - number (next StringValue can handle)
 * $processor->accepts([]);         // false - neither can handle arrays
 *
 * @example
 * // Conversion flow examples
 * $processor = new ToUpper(new Trim(new StringValue()));
 *
 * // Flow: input -> ToUpper -> Trim -> StringValue
 * $result = $processor->convert('  hello  ');
 * // 1. ToUpper: '  hello  ' -> '  HELLO  '
 * // 2. Trim: '  HELLO  ' -> 'HELLO'
 * // 3. StringValue: 'HELLO' -> 'HELLO'
 * // Final result: 'HELLO'
 *
 * @example
 * // API parameter cleaning
 * $apiProcessor = new ToLower(
 *     new Trim(
 *         new StringValue()
 *     )
 * );
 * // GET /api/search?category=  ELECTRONICS
 * if ($apiProcessor->accepts($_GET['category'])) {
 *     $cleanCategory = $apiProcessor->convert($_GET['category']);
 *     // 'electronics' - cleaned and normalized
 * }
 *
 * @example
 * // Multi-step data processing
 * $dataProcessor = new Split(
 *     new ArrayValue(
 *         new ToUpper(
 *             new Trim(new StringValue())
 *         )
 *     ),
 *     '|'
 * );
 * $result = $dataProcessor->convert('  php  |  js  |  python  ');
 * // Results in: ['PHP', 'JS', 'PYTHON']
 *
 * @example
 * // Error handling and validation
 * $safeProcessor = new Trim(new StringValue());
 * if ($safeProcessor->accepts($userInput)) {
 *     $cleanValue = $safeProcessor->convert($userInput);
 *     // Process safely cleaned value
 * } else {
 *     // Handle invalid input that no accessor can process
 * }
 *
 * @example
 * // Database preparation
 * $dbProcessor = new Trim(
 *     new StringValue(true) // Allow empty strings
 * );
 * $dbFilter = new Map([
 *     'title' => new Like('title', $dbProcessor),
 *     'description' => new Like('description', $dbProcessor)
 * ]);
 * // Ensures all text fields are trimmed before database operations
 *
 * @example
 * // Content management preprocessing
 * $contentProcessor = new Trim(new StringValue());
 * $slugProcessor = new ToLower(
 *     new Trim(
 *         new RegexValue('/^[a-z0-9-]+$/')
 *     )
 * );
 * $cmsFilter = new Map([
 *     'title' => new Equals('title', $contentProcessor),
 *     'slug' => new Equals('slug', $slugProcessor)
 * ]);
 *
 * Implementation details:
 * - acceptsCurrent(): Check if current accessor can handle the input
 * - convertCurrent(): Transform input using current accessor's logic
 * - Chain execution: Current processes first, then passes to next
 * - Accepts if ANY accessor in chain can handle the input
 * - Conversion flows through entire chain in order
 *
 * Available concrete accessors:
 * - Trim: Remove whitespace from string edges
 * - ToUpper: Convert strings to uppercase
 * - ToLower: Convert strings to lowercase
 * - Split: Split strings into arrays using delimiter
 *
 * Important notes:
 * - Accessors process values BEFORE final validation
 * - Multiple accessors can be chained for complex transformations
 * - Order matters: transformations apply left-to-right
 * - Each accessor should handle specific input types appropriately
 * - Final ValueInterface in chain performs actual type validation
 * - Useful for data cleaning, normalization, and user input processing
 */
abstract class Accessor implements ValueInterface
{
    public function __construct(
        protected ValueInterface $next,
    ) {}

    final public function accepts(mixed $value): bool
    {
        return $this->acceptsCurrent($value) || $this->next->accepts($value);
    }

    final public function convert(mixed $value): mixed
    {
        return $this->next->convert($this->convertCurrent($value));
    }

    abstract protected function acceptsCurrent(mixed $value): bool;

    abstract protected function convertCurrent(mixed $value): mixed;
}
