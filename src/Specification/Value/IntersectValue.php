<?php

declare(strict_types=1);

namespace Spiral\DataGrid\Specification\Value;

use Spiral\DataGrid\Specification\ValueInterface;

/**
 * Validates arrays where at least one element matches any value from a predefined enum set.
 * This is useful for "contains any of" scenarios where partial matches are acceptable.
 *
 * Difference from SubsetValue:
 * - IntersectValue: At least one match required (ANY logic)
 * - SubsetValue: All elements must match (ALL logic)
 *
 * ```
 * // Tag-based content filtering
 * $tagIntersectValue = new IntersectValue(
 *     new StringValue(),
 *     'programming', 'tutorial', 'beginner', 'advanced'
 * );
 * $contentFilter = new InArray('tags', $tagIntersectValue);
 * $result = $contentFilter->withValue(['cooking', 'programming', 'music']);
 * // Matches because 'programming' is in the allowed set
 * ```
 * ```
 * // Skill-based job matching
 * $skillIntersectValue = new IntersectValue(
 *     new StringValue(),
 *     'php', 'javascript', 'python', 'java', 'react'
 * );
 * $jobFilter = new InArray('required_skills', $skillIntersectValue);
 * $result = $jobFilter->withValue(['php', 'mysql', 'linux']);
 * // Matches because candidate has 'php' which is required
 * ```
 * ```
 * // Product category intersection
 * $categoryIntersectValue = new IntersectValue(
 *     new StringValue(),
 *     'electronics', 'computers', 'mobile', 'gaming'
 * );
 * $productFilter = new InArray('categories', $categoryIntersectValue);
 * $result = $productFilter->withValue(['mobile', 'accessories', 'bluetooth']);
 * // Matches because 'mobile' is in the target categories
 * ```
 * ```
 * // Permission validation (user needs ANY of these permissions)
 * $permissionIntersectValue = new IntersectValue(
 *     new StringValue(),
 *     'admin', 'moderator', 'editor'
 * );
 * $accessFilter = new InArray('user_permissions', $permissionIntersectValue);
 * $result = $accessFilter->withValue(['user', 'editor', 'commenter']);
 * // Access granted because user has 'editor' permission
 * ```
 * ```
 * // Language preference matching
 * $languageIntersectValue = new IntersectValue(
 *     new StringValue(),
 *     'en', 'es', 'fr', 'de'
 * );
 * $contentFilter = new InArray('available_languages', $languageIntersectValue);
 * $result = $contentFilter->withValue(['zh', 'en', 'ja']);
 * // Matches because content is available in 'en' (English)
 * ```
 * ```
 * // Feature compatibility checking
 * $featureIntersectValue = new IntersectValue(
 *     new StringValue(),
 *     'bluetooth', 'wifi', 'gps', 'camera'
 * );
 * $deviceFilter = new InArray('supported_features', $featureIntersectValue);
 * $result = $deviceFilter->withValue(['bluetooth', 'usb', 'sdcard']);
 * // Compatible because device supports 'bluetooth'
 * ```
 * ```
 * // Geographic region intersection
 * $regionIntersectValue = new IntersectValue(
 *     new StringValue(),
 *     'north-america', 'europe', 'asia-pacific'
 * );
 * $serviceFilter = new InArray('service_regions', $regionIntersectValue);
 * $result = $serviceFilter->withValue(['europe', 'middle-east', 'africa']);
 * // Available because service covers 'europe'
 * ```
 * ```
 * // Software version compatibility
 * $versionIntersectValue = new IntersectValue(
 *     new StringValue(),
 *     'php-8.0', 'php-8.1', 'php-8.2', 'php-8.3'
 * );
 * $softwareFilter = new InArray('compatible_versions', $versionIntersectValue);
 * $result = $softwareFilter->withValue(['php-7.4', 'php-8.1', 'php-8.2']);
 * // Compatible with modern PHP versions (8.1, 8.2)
 * ```
 * ```
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
 * ```
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
