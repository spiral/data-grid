<?php

declare(strict_types=1);

namespace Spiral\DataGrid\Specification\Value\Accessor;

use Spiral\DataGrid\Specification\ValueInterface;

/**
 * String splitting accessor that transforms strings into arrays using a specified delimiter.
 * Useful for processing comma-separated values, tag lists, or any delimited string data.
 *
 * Real-world usage examples:
 * - Tag processing: Convert "php,javascript,python" into individual tags
 * - CSV field parsing: Split delimited data fields
 * - Multi-select form inputs: Process checkbox/select multiple values
 * - Search terms: Split search queries into individual terms
 * - Category lists: Parse category hierarchies or multiple categories
 * - Permission strings: Split role/permission lists
 * - Configuration values: Parse comma-separated config options
 * - Import data processing: Clean and split external data formats
 *
 * Common delimiters:
 * - Comma: ',' (default) - most common for lists
 * - Pipe: '|' - alternative separator
 * - Semicolon: ';' - European CSV standard
 * - Space: ' ' - word separation
 * - Custom: any string delimiter
 *
 * @example
 * // Basic tag splitting (comma-separated)
 * $tagSplitter = new Split(new ArrayValue(new StringValue()));
 * $tagFilter = new InArray('tags', $tagSplitter);
 * $result = $tagFilter->withValue('php,javascript,python');
 * // Results in: ['php', 'javascript', 'python']
 *
 * @example
 * // Custom delimiter (pipe-separated)
 * $pipeSplitter = new Split(new ArrayValue(new StringValue()), '|');
 * $categoryFilter = new InArray('categories', $pipeSplitter);
 * $result = $categoryFilter->withValue('electronics|computers|mobile');
 * // Results in: ['electronics', 'computers', 'mobile']
 *
 * @example
 * // Combined with trimming for clean results
 * $cleanTagSplitter = new Split(
 *     new ArrayValue(new Trim(new StringValue())),
 *     ','
 * );
 * $result = $cleanTagSplitter->convert('  php  ,  javascript  ,  python  ');
 * // Results in: ['php', 'javascript', 'python'] (trimmed)
 *
 * @example
 * // Search term splitting
 * $searchSplitter = new Split(
 *     new ArrayValue(new StringValue()),
 *     ' '
 * );
 * $searchFilter = new InArray('search_terms', $searchSplitter);
 * $result = $searchFilter->withValue('php tutorial beginner');
 * // Results in: ['php', 'tutorial', 'beginner']
 *
 * @example
 * // Permission list processing
 * $permissionSplitter = new Split(
 *     new ArrayValue(
 *         new EnumValue(new StringValue(), 'read', 'write', 'admin', 'delete')
 *     ),
 *     ','
 * );
 * $permissionFilter = new InArray('permissions', $permissionSplitter);
 * $result = $permissionFilter->withValue('read,write,admin');
 * // Results in: ['read', 'write', 'admin'] (validated against enum)
 *
 * @example
 * // Email list splitting
 * $emailSplitter = new Split(
 *     new ArrayValue(
 *         new Trim(new RegexValue('/^[^@]+@[^@]+\.[^@]+$/'))
 *     ),
 *     ','
 * );
 * $emailFilter = new InArray('email_list', $emailSplitter);
 * $result = $emailFilter->withValue('user1@example.com, user2@example.com');
 * // Results in validated email array
 *
 * @example
 * // Product SKU list processing
 * $skuSplitter = new Split(
 *     new ArrayValue(new StringValue()),
 *     ';'
 * );
 * $productFilter = new InArray('sku_list', $skuSplitter);
 * $result = $productFilter->withValue('ABC-123;DEF-456;GHI-789');
 * // Results in: ['ABC-123', 'DEF-456', 'GHI-789']
 *
 * @example
 * // Multi-level category splitting
 * $categorySplitter = new Split(
 *     new ArrayValue(new StringValue()),
 *     '>'
 * );
 * $result = $categorySplitter->convert('Electronics>Computers>Laptops');
 * // Results in: ['Electronics', 'Computers', 'Laptops']
 *
 * @example
 * // Validation examples
 * $splitter = new Split(new ArrayValue(new StringValue()));
 *
 * // Valid inputs (strings that can be split)
 * $splitter->accepts('tag1,tag2,tag3');     // true - string with delimiters
 * $splitter->accepts('single-tag');         // true - single string
 * $splitter->accepts('');                   // true - empty string
 * $splitter->accepts('no-delimiter');       // true - string without delimiter
 *
 * // Invalid inputs (non-strings or arrays that can't be handled by ArrayValue)
 * $splitter->accepts(['already', 'array']); // depends on next ValueInterface
 * $splitter->accepts(123);                  // false - number
 * $splitter->accepts(null);                 // false - null
 *
 * @example
 * // Conversion examples
 * $splitter = new Split(new ArrayValue(new StringValue()), ',');
 *
 * $splitter->convert('a,b,c');              // Returns ['a', 'b', 'c']
 * $splitter->convert('single');             // Returns ['single']
 * $splitter->convert('');                   // Returns [''] (empty string in array)
 * $splitter->convert('a,,c');               // Returns ['a', '', 'c'] (empty middle element)
 * $splitter->convert(',b,');                // Returns ['', 'b', ''] (empty start/end)
 *
 * @example
 * // Form processing: multi-select values
 * $multiSelectSplitter = new Split(
 *     new ArrayValue(new StringValue()),
 *     ','
 * );
 * // HTML: <input type="hidden" name="selected_items" value="item1,item2,item3">
 * if ($multiSelectSplitter->accepts($_POST['selected_items'])) {
 *     $selectedItems = $multiSelectSplitter->convert($_POST['selected_items']);
 *     // Process array of selected items
 * }
 *
 * @example
 * // API parameter processing
 * $apiSplitter = new Split(
 *     new ArrayValue(new IntValue()),
 *     ','
 * );
 * // GET /api/users?ids=1,5,10,15
 * if ($apiSplitter->accepts($_GET['ids'])) {
 *     $userIds = $apiSplitter->convert($_GET['ids']);
 *     // Results in: [1, 5, 10, 15] (integers)
 * }
 *
 * @example
 * // Configuration file processing
 * $configSplitter = new Split(
 *     new ArrayValue(new StringValue()),
 *     ','
 * );
 * $configFilter = new Map([
 *     'allowed_hosts' => new InArray('hosts', $configSplitter),
 *     'admin_emails' => new InArray('emails', $configSplitter)
 * ]);
 * // Config: allowed_hosts=localhost,127.0.0.1,example.com
 *
 * @example
 * // CSV data processing
 * $csvSplitter = new Split(
 *     new ArrayValue(new Trim(new StringValue())),
 *     ','
 * );
 * $csvFilter = new InArray('csv_row', $csvSplitter);
 * $result = $csvFilter->withValue('John Doe, 30, Engineer');
 * // Results in: ['John Doe', '30', 'Engineer'] (trimmed)
 *
 * @example
 * // Social media hashtag processing
 * $hashtagSplitter = new Split(
 *     new ArrayValue(
 *         new RegexValue('/^#[a-zA-Z0-9_]+$/')
 *     ),
 *     ' '
 * );
 * $socialFilter = new InArray('hashtags', $hashtagSplitter);
 * $result = $socialFilter->withValue('#php #programming #tutorial');
 * // Results in validated hashtag array
 *
 * @example
 * // Geographic coordinate splitting
 * $coordSplitter = new Split(
 *     new ArrayValue(new FloatValue()),
 *     ','
 * );
 * $locationFilter = new InArray('coordinates', $coordSplitter);
 * $result = $locationFilter->withValue('40.7128,-74.0060');
 * // Results in: [40.7128, -74.0060] (floats)
 *
 * @example
 * // Multi-language content processing
 * $languageSplitter = new Split(
 *     new ArrayValue(
 *         new EnumValue(new StringValue(), 'en', 'es', 'fr', 'de', 'it')
 *     ),
 *     ','
 * );
 * $contentFilter = new InArray('languages', $languageSplitter);
 * $result = $contentFilter->withValue('en,es,fr');
 * // Results in validated language array
 *
 * @example
 * // File extension processing
 * $extensionSplitter = new Split(
 *     new ArrayValue(new StringValue()),
 *     ','
 * );
 * $fileFilter = new InArray('allowed_extensions', $extensionSplitter);
 * $result = $fileFilter->withValue('jpg,png,gif,webp');
 * // Results in: ['jpg', 'png', 'gif', 'webp']
 *
 * @example
 * // Time slot processing
 * $timeSplitter = new Split(
 *     new ArrayValue(new RegexValue('/^\d{2}:\d{2}$/')),
 *     ','
 * );
 * $scheduleFilter = new InArray('time_slots', $timeSplitter);
 * $result = $scheduleFilter->withValue('09:00,13:30,17:45');
 * // Results in validated time slot array
 *
 * @example
 * // Complex nested processing
 * $complexSplitter = new Split(
 *     new ArrayValue(
 *         new ToUpper(
 *             new Trim(new StringValue())
 *         )
 *     ),
 *     '|'
 * );
 * $result = $complexSplitter->convert('  admin  |  user  |  guest  ');
 * // Results in: ['ADMIN', 'USER', 'GUEST'] (split, trimmed, uppercased)
 *
 * @example
 * // Database import processing
 * $importSplitter = new Split(
 *     new ArrayValue(new StringValue(true)), // Allow empty values
 *     '\t'  // Tab-separated
 * );
 * $importFilter = new InArray('tsv_row', $importSplitter);
 * // Process tab-separated values from database export
 *
 * @example
 * // URL parameter processing
 * $urlSplitter = new Split(
 *     new ArrayValue(new StringValue()),
 *     ','
 * );
 * // URL: /search?categories=electronics,computers,mobile
 * if ($urlSplitter->accepts($_GET['categories'])) {
 *     $categories = $urlSplitter->convert($_GET['categories']);
 *     // Process category filter array
 * }
 *
 * @example
 * // Error handling
 * $safeSplitter = new Split(new ArrayValue(new StringValue()), ',');
 * try {
 *     if ($safeSplitter->accepts($userInput)) {
 *         $splitResult = $safeSplitter->convert($userInput);
 *         // Process successfully split array
 *     } else {
 *         // Handle input that can't be processed
 *     }
 * } catch (\Exception $e) {
 *     // Handle conversion errors
 * }
 *
 * @example
 * // Chaining with other accessors
 * $chainedSplitter = new Split(
 *     new ArrayValue(
 *         new ToLower(
 *             new Trim(new StringValue())
 *         )
 *     ),
 *     ','
 * );
 * $result = $chainedSplitter->convert('  PHP  ,  JAVASCRIPT  ,  PYTHON  ');
 * // Results in: ['php', 'javascript', 'python']
 *
 * Important notes:
 * - Only accepts string input for splitting
 * - Uses PHP's explode() function internally
 * - Empty strings between delimiters are preserved
 * - Single strings without delimiters become single-element arrays
 * - Default delimiter is comma (',')
 * - Each split element is processed by the next ValueInterface in chain
 * - Useful for converting delimited strings to validated arrays
 * - Can be combined with other accessors for complex transformations
 * - Essential for processing form inputs and API parameters with multiple values
 */
class Split extends Accessor
{
    public function __construct(
        ValueInterface $next,
        private readonly string $char = ',',
    ) {
        parent::__construct($next);
    }

    protected function acceptsCurrent(mixed $value): bool
    {
        return \is_string($value);
    }

    protected function convertCurrent(mixed $value): array
    {
        return \explode($this->char, (string) $value);
    }
}
