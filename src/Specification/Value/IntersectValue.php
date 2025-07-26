<?php

declare(strict_types=1);

namespace Spiral\DataGrid\Specification\Value;

use Spiral\DataGrid\Specification\ValueInterface;

/**
 * Validates arrays where at least one element matches any value from a predefined enum set.
 * This is useful for "contains any of" scenarios where partial matches are acceptable.
 *
 * Real-world usage examples:
 * - Tag filtering: Find content that has ANY of the specified tags
 * - Skill matching: Find users with ANY of the required skills
 * - Category intersection: Products that belong to ANY of the selected categories
 * - Permission checking: Users with ANY of the specified permissions
 * - Feature filtering: Items that support ANY of the requested features
 * - Location matching: Find items available in ANY of the specified regions
 * - Language support: Content available in ANY of the preferred languages
 * - Compatibility checking: Software that works with ANY of the specified versions
 *
 * Difference from SubsetValue:
 * - IntersectValue: At least one match required (ANY logic)
 * - SubsetValue: All elements must match (ALL logic)
 *
 * @example
 * // Tag-based content filtering
 * $tagIntersectValue = new IntersectValue(
 *     new StringValue(),
 *     'programming', 'tutorial', 'beginner', 'advanced'
 * );
 * $contentFilter = new InArray('tags', $tagIntersectValue);
 * $result = $contentFilter->withValue(['cooking', 'programming', 'music']);
 * // Matches because 'programming' is in the allowed set
 *
 * @example
 * // Skill-based job matching
 * $skillIntersectValue = new IntersectValue(
 *     new StringValue(),
 *     'php', 'javascript', 'python', 'java', 'react'
 * );
 * $jobFilter = new InArray('required_skills', $skillIntersectValue);
 * $result = $jobFilter->withValue(['php', 'mysql', 'linux']);
 * // Matches because candidate has 'php' which is required
 *
 * @example
 * // Product category intersection
 * $categoryIntersectValue = new IntersectValue(
 *     new StringValue(),
 *     'electronics', 'computers', 'mobile', 'gaming'
 * );
 * $productFilter = new InArray('categories', $categoryIntersectValue);
 * $result = $productFilter->withValue(['mobile', 'accessories', 'bluetooth']);
 * // Matches because 'mobile' is in the target categories
 *
 * @example
 * // Permission validation (user needs ANY of these permissions)
 * $permissionIntersectValue = new IntersectValue(
 *     new StringValue(),
 *     'admin', 'moderator', 'editor'
 * );
 * $accessFilter = new InArray('user_permissions', $permissionIntersectValue);
 * $result = $accessFilter->withValue(['user', 'editor', 'commenter']);
 * // Access granted because user has 'editor' permission
 *
 * @example
 * // Language preference matching
 * $languageIntersectValue = new IntersectValue(
 *     new StringValue(),
 *     'en', 'es', 'fr', 'de'
 * );
 * $contentFilter = new InArray('available_languages', $languageIntersectValue);
 * $result = $contentFilter->withValue(['zh', 'en', 'ja']);
 * // Matches because content is available in 'en' (English)
 *
 * @example
 * // Feature compatibility checking
 * $featureIntersectValue = new IntersectValue(
 *     new StringValue(),
 *     'bluetooth', 'wifi', 'gps', 'camera'
 * );
 * $deviceFilter = new InArray('supported_features', $featureIntersectValue);
 * $result = $deviceFilter->withValue(['bluetooth', 'usb', 'sdcard']);
 * // Compatible because device supports 'bluetooth'
 *
 * @example
 * // Geographic region intersection
 * $regionIntersectValue = new IntersectValue(
 *     new StringValue(),
 *     'north-america', 'europe', 'asia-pacific'
 * );
 * $serviceFilter = new InArray('service_regions', $regionIntersectValue);
 * $result = $serviceFilter->withValue(['europe', 'middle-east', 'africa']);
 * // Available because service covers 'europe'
 *
 * @example
 * // Software version compatibility
 * $versionIntersectValue = new IntersectValue(
 *     new StringValue(),
 *     'php-8.0', 'php-8.1', 'php-8.2', 'php-8.3'
 * );
 * $softwareFilter = new InArray('compatible_versions', $versionIntersectValue);
 * $result = $softwareFilter->withValue(['php-7.4', 'php-8.1', 'php-8.2']);
 * // Compatible with modern PHP versions (8.1, 8.2)
 *
 * @example
 * // Validation examples
 * $intersectValue = new IntersectValue(new StringValue(), 'red', 'blue', 'green');
 *
 * // Valid inputs (at least one match)
 * $intersectValue->accepts('red');                    // true - single match
 * $intersectValue->accepts(['red']);                  // true - array with match
 * $intersectValue->accepts(['red', 'yellow']);        // true - partial match
 * $intersectValue->accepts(['yellow', 'blue']);       // true - has 'blue'
 * $intersectValue->accepts(['red', 'blue', 'green']); // true - all match
 *
 * // Invalid inputs (no matches)
 * $intersectValue->accepts(['yellow']);               // false - no match
 * $intersectValue->accepts(['black', 'white']);       // false - no matches
 * $intersectValue->accepts([]);                       // false - empty array
 *
 * @example
 * // Conversion examples
 * $intersectValue = new IntersectValue(new StringValue(), 'a', 'b', 'c');
 *
 * $intersectValue->convert('b');           // Returns ['b']
 * $intersectValue->convert(['a', 'x']);    // Returns ['a'] (only matching values)
 * $intersectValue->convert(['a', 'b', 'x']); // Returns ['a', 'b']
 *
 * @example
 * // Job application filtering
 * $jobSkillsValue = new IntersectValue(
 *     new StringValue(),
 *     'project-management', 'leadership', 'communication', 'analysis'
 * );
 * $applicantFilter = new InArray('soft_skills', $jobSkillsValue);
 * $result = $applicantFilter->withValue(['teamwork', 'communication', 'creativity']);
 * // Qualified because applicant has 'communication' skill
 *
 * @example
 * // Content recommendation system
 * $interestIntersectValue = new IntersectValue(
 *     new StringValue(),
 *     'technology', 'science', 'business', 'health'
 * );
 * $userFilter = new InArray('interests', $interestIntersectValue);
 * $result = $userFilter->withValue(['cooking', 'technology', 'travel']);
 * // Recommend tech content because user interested in 'technology'
 *
 * @example
 * // API access validation
 * $apiScopeValue = new IntersectValue(
 *     new StringValue(),
 *     'read:users', 'write:users', 'read:orders', 'admin:all'
 * );
 * $tokenFilter = new InArray('token_scopes', $apiScopeValue);
 * $result = $tokenFilter->withValue(['read:users', 'read:profile', 'write:comments']);
 * // Access granted for user operations (has 'read:users')
 *
 * @example
 * // E-commerce filtering
 * $brandIntersectValue = new IntersectValue(
 *     new StringValue(),
 *     'apple', 'samsung', 'google', 'microsoft'
 * );
 * $productFilter = new InArray('preferred_brands', $brandIntersectValue);
 * $result = $productFilter->withValue(['apple', 'sony', 'nintendo']);
 * // Show products because user likes 'apple' brand
 *
 * @example
 * // Social media content filtering
 * $hashtagIntersectValue = new IntersectValue(
 *     new StringValue(),
 *     'trending', 'viral', 'popular', 'featured'
 * );
 * $postFilter = new InArray('hashtags', $hashtagIntersectValue);
 * $result = $postFilter->withValue(['funny', 'viral', 'meme']);
 * // Show post because it has 'viral' hashtag
 *
 * @example
 * // Educational course matching
 * $topicIntersectValue = new IntersectValue(
 *     new StringValue(),
 *     'programming', 'data-science', 'machine-learning', 'web-development'
 * );
 * $courseFilter = new InArray('course_topics', $topicIntersectValue);
 * $result = $courseFilter->withValue(['programming', 'mobile-development', 'design']);
 * // Recommend because includes 'programming' topic
 *
 * Important notes:
 * - Single values are converted to arrays for processing
 * - Only returns values that match the enum set
 * - At least one match is required for acceptance
 * - More permissive than SubsetValue (which requires ALL matches)
 * - Useful for "OR" logic scenarios in filtering
 * - Maintains order of matched elements
 * - Empty results after filtering are rejected
 */
final class IntersectValue implements ValueInterface
{
    private readonly ValueInterface $enum;

    public function __construct(ValueInterface $enum, mixed ...$values)
    {
        $this->enum = new EnumValue($enum, ...$values);
    }

    public function accepts(mixed $value): bool
    {
        $value = (array) $value;

        if (\count($value) === 1) {
            return $this->enum->accepts(\array_values($value)[0]);
        }

        foreach ($value as $v) {
            if ($this->enum->accepts($v)) {
                return true;
            }
        }

        return false;
    }

    public function convert(mixed $value): array
    {
        $result = [];
        foreach ((array) $value as $v) {
            if ($this->enum->accepts($v)) {
                $result[] = $this->enum->convert($v);
            }
        }

        return $result;
    }
}
