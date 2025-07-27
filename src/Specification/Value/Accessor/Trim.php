<?php

declare(strict_types=1);

namespace Spiral\DataGrid\Specification\Value\Accessor;

/**
 * String trimming accessor that removes whitespace from the beginning and end of strings.
 * Essential for cleaning user input and ensuring consistent data processing.
 *
 * Whitespace characters removed:
 * - Spaces: ' '
 * - Tabs: '\t'
 * - Newlines: '\n', '\r'
 * - Vertical tabs: '\v'
 * - Form feeds: '\f'
 * - Null bytes: '\0'
 *
 * ```
 * // Basic whitespace trimming
 * $trimmedString = new Trim(new StringValue());
 * $userFilter = new Equals('username', $trimmedString);
 * $result = $userFilter->withValue('  john_doe  '); // Converts to 'john_doe'
 * ```
 * ```
 * // Search query cleaning
 * $searchProcessor = new Trim(
 *     new StringValue()
 * );
 * $searchFilter = new Like('content', $searchProcessor);
 * $result = $searchFilter->withValue('  search term  '); // Clean search: 'search term'
 * ```
 * ```
 * // Form field processing
 * $nameProcessor = new Trim(new StringValue());
 * $formFilter = new Map([
 *     'first_name' => new Equals('first_name', $nameProcessor),
 *     'last_name' => new Equals('last_name', $nameProcessor),
 *     'company' => new Equals('company', new Trim(new StringValue(true))) // Optional field
 * ]);
 * // Cleans all name fields: '  John  ' -> 'John'
 * ```
 * ```
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
 * ```
 * ```
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
 * ```
 * ```
 * // Configuration value cleaning
 * $configProcessor = new Trim(
 *     new EnumValue(new StringValue(), 'debug', 'info', 'warning', 'error')
 * );
 * $configFilter = new Equals('log_level', $configProcessor);
 * $result = $configFilter->withValue('  debug  '); // Converts to 'debug'
 * ```
 * ```
 * // User registration processing
 * $registrationProcessor = new Trim(new StringValue());
 * $userFilter = new All(
 *     new Equals('username', $registrationProcessor),
 *     new Equals('email', new Trim(new RegexValue('/^[^@]+@[^@]+\.[^@]+$/'))),
 *     new Like('full_name', $registrationProcessor)
 * );
 * // Ensures all user input is properly trimmed
 * ```
 * ```
 * // API parameter cleaning
 * $apiProcessor = new Trim(new StringValue());
 * // GET /api/users?name=  John  Doe
 * if ($apiProcessor->accepts($_GET['name'])) {
 *     $cleanName = $apiProcessor->convert($_GET['name']);
 *     // Results in: 'John  Doe' (outer spaces removed, inner preserved)
 * }
 * ```
 * ```
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
 * ```
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
