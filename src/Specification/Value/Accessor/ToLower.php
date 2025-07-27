<?php

declare(strict_types=1);

namespace Spiral\DataGrid\Specification\Value\Accessor;

/**
 * String lowercase conversion accessor that transforms strings to lowercase before validation.
 * Useful for case-insensitive processing, search normalization, and data standardization.
 *
 * ```
 * // Basic lowercase conversion
 * $lowercaseString = new ToLower(new StringValue());
 * $userFilter = new Equals('username', $lowercaseString);
 * $result = $userFilter->withValue('JohnDoe'); // Converts to 'johndoe'
 * ```
 * ```
 * // Search term normalization
 * $searchProcessor = new ToLower(
 *     new Trim(new StringValue())
 * );
 * $searchFilter = new Like('content', $searchProcessor);
 * $result = $searchFilter->withValue('  SEARCH TERM  ');
 * // Enables case-insensitive search: 'search term'
 * ```
 * ```
 * // Tag standardization
 * $tagProcessor = new Split(
 *     new ArrayValue(
 *         new ToLower(
 *             new Trim(new StringValue())
 *         )
 *     ),
 *     ','
 * );
 * $tagFilter = new InArray('tags', $tagProcessor);
 * $result = $tagFilter->withValue('PHP, JAVASCRIPT, Python');
 * // Results in: ['php', 'javascript', 'python']
 * ```
 * ```
 * // URL slug processing
 * $slugProcessor = new ToLower(
 *     new RegexValue('/^[a-z0-9-]+$/')
 * );
 * $slugFilter = new Equals('url_slug', $slugProcessor);
 * $result = $slugFilter->withValue('My-Article-Title'); // Converts to 'my-article-title'
 * ```
 * ```
 * // Case-insensitive enum validation
 * $statusProcessor = new ToLower(
 *     new EnumValue(new StringValue(), 'active', 'inactive', 'pending', 'banned')
 * );
 * $statusFilter = new Equals('status', $statusProcessor);
 * $result = $statusFilter->withValue('ACTIVE'); // Converts to 'active'
 * ```
 * ```
 * // Content management system
 * $contentProcessor = new ToLower(
 *     new Trim(new StringValue())
 * );
 * $cmsFilter = new Map([
 *     'category' => new Equals('category', $contentProcessor),
 *     'status' => new Equals('status', $contentProcessor),
 *     'author' => new Equals('author', $contentProcessor)
 * ]);
 * // Normalizes all content metadata to lowercase
 * ```
 * ```
 * // Permission system processing
 * $permissionProcessor = new Split(
 *     new ArrayValue(
 *         new ToLower(
 *             new EnumValue(new StringValue(), 'read', 'write', 'admin', 'delete')
 *         )
 *     ),
 *     ','
 * );
 * $result = $permissionProcessor->convert('READ,WRITE,Admin');
 * // Results in: ['read', 'write', 'admin']
 * ```
 * Important notes:
 * - Only processes string values directly
 * - Uses PHP's strtolower() function
 * - Preserves non-alphabetic characters unchanged
 * - Useful for case-insensitive operations
 * - Can be chained with other accessors
 * - Essential for data normalization and search functionality
 * - Helps prevent duplicate entries due to case differences
 * - Improves user experience by handling case variations automatically
 */
final class ToLower extends Accessor
{
    protected function acceptsCurrent(mixed $value): bool
    {
        return \is_string($value);
    }

    protected function convertCurrent(mixed $value): mixed
    {
        return \is_string($value) ? \strtolower($value) : $value;
    }
}
