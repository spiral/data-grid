<?php

declare(strict_types=1);

namespace Spiral\DataGrid\Specification\Value\Accessor;

/**
 * String lowercase conversion accessor that transforms strings to lowercase before validation.
 * Useful for case-insensitive processing, search normalization, and data standardization.
 *
 * Real-world usage examples:
 * - Case-insensitive search: Normalize search terms for consistent matching
 * - User input standardization: Convert usernames, emails to lowercase
 * - Database normalization: Ensure consistent case for stored values
 * - Configuration processing: Normalize config values to lowercase
 * - Tag processing: Standardize tag case for consistency
 * - URL slug generation: Create lowercase URL-friendly strings
 * - API parameter normalization: Handle case variations in parameters
 * - Content management: Standardize categories, keywords, labels
 *
 * Benefits:
 * - Consistent data storage and retrieval
 * - Improved search functionality
 * - Case-insensitive comparisons
 * - Reduced duplicate entries due to case differences
 * - Better user experience (users don't worry about case)
 *
 * @example
 * // Basic lowercase conversion
 * $lowercaseString = new ToLower(new StringValue());
 * $userFilter = new Equals('username', $lowercaseString);
 * $result = $userFilter->withValue('JohnDoe'); // Converts to 'johndoe'
 *
 * @example
 * // Case-insensitive email processing
 * $emailProcessor = new ToLower(
 *     new Trim(
 *         new RegexValue('/^[^@]+@[^@]+\.[^@]+$/')
 *     )
 * );
 * $emailFilter = new Equals('email', $emailProcessor);
 * $result = $emailFilter->withValue('  USER@EXAMPLE.COM  ');
 * // Results in: 'user@example.com' (trimmed and lowercased)
 *
 * @example
 * // Search term normalization
 * $searchProcessor = new ToLower(
 *     new Trim(new StringValue())
 * );
 * $searchFilter = new Like('content', $searchProcessor);
 * $result = $searchFilter->withValue('  SEARCH TERM  ');
 * // Enables case-insensitive search: 'search term'
 *
 * @example
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
 *
 * @example
 * // Configuration value normalization
 * $configProcessor = new ToLower(
 *     new EnumValue(new StringValue(), 'debug', 'info', 'warning', 'error')
 * );
 * $configFilter = new Equals('log_level', $configProcessor);
 * $result = $configFilter->withValue('DEBUG'); // Converts to 'debug'
 *
 * @example
 * // URL slug processing
 * $slugProcessor = new ToLower(
 *     new RegexValue('/^[a-z0-9-]+$/')
 * );
 * $slugFilter = new Equals('url_slug', $slugProcessor);
 * $result = $slugFilter->withValue('My-Article-Title'); // Converts to 'my-article-title'
 *
 * @example
 * // Category name standardization
 * $categoryProcessor = new ToLower(
 *     new StringValue()
 * );
 * $categoryFilter = new InArray('categories', new ArrayValue($categoryProcessor));
 * $result = $categoryFilter->withValue(['ELECTRONICS', 'Computers', 'Mobile']);
 * // Results in: ['electronics', 'computers', 'mobile']
 *
 * @example
 * // Case-insensitive enum validation
 * $statusProcessor = new ToLower(
 *     new EnumValue(new StringValue(), 'active', 'inactive', 'pending', 'banned')
 * );
 * $statusFilter = new Equals('status', $statusProcessor);
 * $result = $statusFilter->withValue('ACTIVE'); // Converts to 'active'
 *
 * @example
 * // Validation examples
 * $lowercase = new ToLower(new StringValue());
 *
 * // Valid inputs (strings that can be converted)
 * $lowercase->accepts('HELLO');         // true - string
 * $lowercase->accepts('MixedCase');     // true - string
 * $lowercase->accepts('already-lower'); // true - string
 * $lowercase->accepts('');              // depends on StringValue config
 *
 * // Inputs handled by next in chain
 * $lowercase->accepts(123);             // true - StringValue can convert
 * $lowercase->accepts([]);              // false - StringValue can't handle arrays
 * $lowercase->accepts(null);            // false - StringValue can't handle null
 *
 * @example
 * // Conversion examples
 * $lowercase = new ToLower(new StringValue());
 *
 * $lowercase->convert('HELLO');         // Returns 'hello'
 * $lowercase->convert('MixedCase');     // Returns 'mixedcase'
 * $lowercase->convert('Already-Lower'); // Returns 'already-lower'
 * $lowercase->convert('123');           // Returns '123'
 * $lowercase->convert('');              // Returns ''
 *
 * @example
 * // User registration processing
 * $usernameProcessor = new ToLower(
 *     new Trim(
 *         new RegexValue('/^[a-z0-9_]{3,20}$/')
 *     )
 * );
 * $userFilter = new Equals('username', $usernameProcessor);
 * $result = $userFilter->withValue('  JohnDoe123  '); // Becomes 'johndoe123'
 *
 * @example
 * // API parameter processing
 * $apiProcessor = new ToLower(new StringValue());
 * // GET /api/products?category=ELECTRONICS
 * if ($apiProcessor->accepts($_GET['category'])) {
 *     $category = $apiProcessor->convert($_GET['category']);
 *     // Results in: 'electronics'
 * }
 *
 * @example
 * // Database search optimization
 * $searchProcessor = new ToLower(
 *     new StringValue()
 * );
 * $searchFilter = new Like('product_name', $searchProcessor);
 * // Enables case-insensitive product search
 *
 * @example
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
 *
 * @example
 * // Multi-language keyword processing
 * $keywordProcessor = new Split(
 *     new ArrayValue(
 *         new ToLower(
 *             new Trim(new StringValue())
 *         )
 *     ),
 *     ','
 * );
 * $result = $keywordProcessor->convert('ENGLISH, Français, DEUTSCH');
 * // Results in: ['english', 'français', 'deutsch']
 *
 * @example
 * // Social media handle processing
 * $handleProcessor = new ToLower(
 *     new RegexValue('/^@[a-z0-9_]+$/')
 * );
 * $socialFilter = new Equals('twitter_handle', $handleProcessor);
 * $result = $socialFilter->withValue('@JohnDoe123'); // Becomes '@johndoe123'
 *
 * @example
 * // File extension processing
 * $extensionProcessor = new ToLower(
 *     new EnumValue(new StringValue(), 'jpg', 'png', 'gif', 'pdf', 'doc')
 * );
 * $fileFilter = new Equals('file_extension', $extensionProcessor);
 * $result = $fileFilter->withValue('JPG'); // Converts to 'jpg'
 *
 * @example
 * // Form processing with normalization
 * $formProcessor = new ToLower(
 *     new Trim(new StringValue())
 * );
 * $formFilter = new Map([
 *     'country' => new Equals('country', $formProcessor),
 *     'language' => new Equals('language', $formProcessor),
 *     'currency' => new Equals('currency', $formProcessor)
 * ]);
 * // Normalizes form inputs: 'USA' -> 'usa', 'English' -> 'english'
 *
 * @example
 * // E-commerce product processing
 * $productProcessor = new ToLower(new StringValue());
 * $productFilter = new All(
 *     new Like('brand', $productProcessor),
 *     new InArray('categories', new ArrayValue($productProcessor))
 * );
 * // Enables case-insensitive brand and category matching
 *
 * @example
 * // Log level processing
 * $logProcessor = new ToLower(
 *     new EnumValue(new StringValue(), 'debug', 'info', 'warning', 'error', 'critical')
 * );
 * $logFilter = new Equals('log_level', $logProcessor);
 * $result = $logFilter->withValue('ERROR'); // Becomes 'error'
 *
 * @example
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
 *
 * @example
 * // SEO keyword processing
 * $seoProcessor = new Split(
 *     new ArrayValue(
 *         new ToLower(
 *             new Trim(new StringValue())
 *         )
 *     ),
 *     ','
 * );
 * $seoFilter = new InArray('keywords', $seoProcessor);
 * $result = $seoFilter->withValue('SEO, Marketing, DIGITAL');
 * // Results in: ['seo', 'marketing', 'digital']
 *
 * @example
 * // Geographic location processing
 * $locationProcessor = new ToLower(
 *     new Trim(new StringValue())
 * );
 * $locationFilter = new Map([
 *     'country' => new Equals('country', $locationProcessor),
 *     'state' => new Equals('state', $locationProcessor),
 *     'city' => new Equals('city', $locationProcessor)
 * ]);
 * // Normalizes location data: 'NEW YORK' -> 'new york'
 *
 * @example
 * // Data import processing
 * $importProcessor = new ToLower(
 *     new Trim(new StringValue(true)) // Allow empty values
 * );
 * $importFilter = new InArray('csv_row', new ArrayValue($importProcessor));
 * // Normalizes imported data to lowercase
 *
 * @example
 * // Complex chaining example
 * $complexProcessor = new Split(
 *     new ArrayValue(
 *         new ToLower(
 *             new Trim(
 *                 new EnumValue(new StringValue(), 'admin', 'user', 'guest', 'moderator')
 *             )
 *         )
 *     ),
 *     ','
 * );
 * $result = $complexProcessor->convert('  ADMIN  ,  User  ,  GUEST  ');
 * // Results in: ['admin', 'user', 'guest'] (split, trimmed, lowercased, validated)
 *
 * @example
 * // Error handling
 * $safeProcessor = new ToLower(new StringValue());
 * if ($safeProcessor->accepts($userInput)) {
 *     $normalized = $safeProcessor->convert($userInput);
 *     // Process normalized lowercase string
 * } else {
 *     // Handle non-string input
 * }
 *
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
