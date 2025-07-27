<?php

declare(strict_types=1);

namespace Spiral\DataGrid\Specification\Value\Accessor;

use Spiral\DataGrid\Specification\ValueInterface;

/**
 * String splitting accessor that transforms strings into arrays using a specified delimiter.
 * Useful for processing comma-separated values, tag lists, or any delimited string data.
 *
 * ```
 * // Basic tag splitting (comma-separated)
 * $tagSplitter = new Split(new ArrayValue(new StringValue()));
 * $tagFilter = new InArray('tags', $tagSplitter);
 * $result = $tagFilter->withValue('php,javascript,python');
 * // Results in: ['php', 'javascript', 'python']
 * ```
 * ```
 * // Custom delimiter (pipe-separated)
 * $pipeSplitter = new Split(new ArrayValue(new StringValue()), '|');
 * $categoryFilter = new InArray('categories', $pipeSplitter);
 * $result = $categoryFilter->withValue('electronics|computers|mobile');
 * // Results in: ['electronics', 'computers', 'mobile']
 * ```
 * ```
 * // Combined with trimming for clean results
 * $cleanTagSplitter = new Split(
 *     new ArrayValue(new Trim(new StringValue())),
 *     ','
 * );
 * $result = $cleanTagSplitter->convert('  php  ,  javascript  ,  python  ');
 * // Results in: ['php', 'javascript', 'python'] (trimmed)
 * ```
 * ```
 * // Search term splitting
 * $searchSplitter = new Split(
 *     new ArrayValue(new StringValue()),
 *     ' '
 * );
 * $searchFilter = new InArray('search_terms', $searchSplitter);
 * $result = $searchFilter->withValue('php tutorial beginner');
 * // Results in: ['php', 'tutorial', 'beginner']
 * ```
 * ```
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
 * ```
 * ```
 * // Product SKU list processing
 * $skuSplitter = new Split(
 *     new ArrayValue(new StringValue()),
 *     ';'
 * );
 * $productFilter = new InArray('sku_list', $skuSplitter);
 * $result = $productFilter->withValue('ABC-123;DEF-456;GHI-789');
 * // Results in: ['ABC-123', 'DEF-456', 'GHI-789']
 * ```
 * ```
 * // Multi-level category splitting
 * $categorySplitter = new Split(
 *     new ArrayValue(new StringValue()),
 *     '>'
 * );
 * $result = $categorySplitter->convert('Electronics>Computers>Laptops');
 * // Results in: ['Electronics', 'Computers', 'Laptops']
 * ```
 * ```
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
 * ```
 * ```
 * // Conversion examples
 * $splitter = new Split(new ArrayValue(new StringValue()), ',');
 *
 * $splitter->convert('a,b,c');              // Returns ['a', 'b', 'c']
 * $splitter->convert('single');             // Returns ['single']
 * $splitter->convert('');                   // Returns [''] (empty string in array)
 * $splitter->convert('a,,c');               // Returns ['a', '', 'c'] (empty middle element)
 * $splitter->convert(',b,');                // Returns ['', 'b', ''] (empty start/end)
 * ```
 * ```
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
 * ```
 * ```
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
 * ```
 * ```
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
 * ```
 * ```
 * // CSV data processing
 * $csvSplitter = new Split(
 *     new ArrayValue(new Trim(new StringValue())),
 *     ','
 * );
 * $csvFilter = new InArray('csv_row', $csvSplitter);
 * $result = $csvFilter->withValue('John Doe, 30, Engineer');
 * // Results in: ['John Doe', '30', 'Engineer'] (trimmed)
 * ```
 * ```
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
 * ```
 * ```
 * // Geographic coordinate splitting
 * $coordSplitter = new Split(
 *     new ArrayValue(new FloatValue()),
 *     ','
 * );
 * $locationFilter = new InArray('coordinates', $coordSplitter);
 * $result = $locationFilter->withValue('40.7128,-74.0060');
 * // Results in: [40.7128, -74.0060] (floats)
 * ```
 * ```
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
 * ```
 * ```
 * // File extension processing
 * $extensionSplitter = new Split(
 *     new ArrayValue(new StringValue()),
 *     ','
 * );
 * $fileFilter = new InArray('allowed_extensions', $extensionSplitter);
 * $result = $fileFilter->withValue('jpg,png,gif,webp');
 * // Results in: ['jpg', 'png', 'gif', 'webp']
 * ```
 * ```
 * // Time slot processing
 * $timeSplitter = new Split(
 *     new ArrayValue(new RegexValue('/^\d{2}:\d{2}$/')),
 *     ','
 * );
 * $scheduleFilter = new InArray('time_slots', $timeSplitter);
 * $result = $scheduleFilter->withValue('09:00,13:30,17:45');
 * // Results in validated time slot array
 * ```
 * ```
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
 * ```
 * ```
 * // Database import processing
 * $importSplitter = new Split(
 *     new ArrayValue(new StringValue(true)), // Allow empty values
 *     '\t'  // Tab-separated
 * );
 * $importFilter = new InArray('tsv_row', $importSplitter);
 * // Process tab-separated values from database export
 * ```
 * ```
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
 * ```
 * ```
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
 * ```
 * ```
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
 * ```
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
