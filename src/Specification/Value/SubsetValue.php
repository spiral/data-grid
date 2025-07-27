<?php

declare(strict_types=1);

namespace Spiral\DataGrid\Specification\Value;

use Spiral\DataGrid\Specification\ValueInterface;

/**
 * Validates arrays where ALL elements must exist in the predefined enum set.
 * This is the opposite of IntersectValue - requires complete subset matching rather than partial.
 *
 * Difference from IntersectValue:
 * - SubsetValue: ALL elements must be valid (strict validation)
 * - IntersectValue: At least one element must be valid (permissive validation)
 *
 * ```
 * // Permission system (user must have only valid permissions)
 * $permissionSubsetValue = new SubsetValue(
 *     new StringValue(),
 *     'read', 'write', 'admin', 'moderate', 'delete'
 * );
 * $userFilter = new InArray('permissions', $permissionSubsetValue);
 * $result = $userFilter->withValue(['read', 'write']);        // Valid - all permissions valid
 * $result = $userFilter->withValue(['read', 'invalid']);      // Invalid - contains invalid permission
 * ```
 * ```
 * // Required skills validation (all must be from approved list)
 * $skillSubsetValue = new SubsetValue(
 *     new StringValue(),
 *     'php', 'javascript', 'python', 'react', 'vue', 'mysql'
 * );
 * $jobFilter = new InArray('required_skills', $skillSubsetValue);
 * $result = $jobFilter->withValue(['php', 'mysql']);          // Valid - both skills approved
 * $result = $jobFilter->withValue(['php', 'cobol']);          // Invalid - 'cobol' not approved
 * ```
 * ```
 * // Product feature validation
 * $featureSubsetValue = new SubsetValue(
 *     new StringValue(),
 *     'bluetooth', 'wifi', 'gps', 'camera', 'nfc', '5g'
 * );
 * $deviceFilter = new InArray('supported_features', $featureSubsetValue);
 * $result = $deviceFilter->withValue(['bluetooth', 'wifi']);  // Valid - all features supported
 * $result = $deviceFilter->withValue(['wifi', 'hologram']);   // Invalid - 'hologram' not supported
 * ```
 * ```
 * // Content tag validation (only approved tags allowed)
 * $tagSubsetValue = new SubsetValue(
 *     new StringValue(),
 *     'programming', 'tutorial', 'beginner', 'advanced', 'php', 'javascript'
 * );
 * $contentFilter = new InArray('tags', $tagSubsetValue);
 * $result = $contentFilter->withValue(['php', 'tutorial']);   // Valid - approved tags
 * $result = $contentFilter->withValue(['php', 'spam']);       // Invalid - 'spam' not approved
 * ```
 * ```
 * // User role validation
 * $roleSubsetValue = new SubsetValue(
 *     new StringValue(),
 *     'admin', 'editor', 'author', 'contributor', 'subscriber'
 * );
 * $userFilter = new InArray('roles', $roleSubsetValue);
 * $result = $userFilter->withValue(['editor', 'author']);     // Valid - valid roles
 * $result = $userFilter->withValue(['admin', 'hacker']);      // Invalid - 'hacker' not valid role
 * ```
 * ```
 * // Language support validation
 * $languageSubsetValue = new SubsetValue(
 *     new StringValue(),
 *     'en', 'es', 'fr', 'de', 'it', 'pt', 'zh', 'ja'
 * );
 * $contentFilter = new InArray('languages', $languageSubsetValue);
 * $result = $contentFilter->withValue(['en', 'es']);          // Valid - supported languages
 * $result = $contentFilter->withValue(['en', 'klingon']);     // Invalid - 'klingon' not supported
 * ```
 * ```
 * // Category validation (must be from valid categories)
 * $categorySubsetValue = new SubsetValue(
 *     new StringValue(),
 *     'electronics', 'clothing', 'books', 'sports', 'home', 'garden'
 * );
 * $productFilter = new InArray('categories', $categorySubsetValue);
 * $result = $productFilter->withValue(['electronics', 'home']); // Valid categories
 * $result = $productFilter->withValue(['books', 'weapons']);    // Invalid - 'weapons' not allowed
 * ```
 * ```
 * // Configuration validation (all settings must be valid)
 * $configSubsetValue = new SubsetValue(
 *     new StringValue(),
 *     'debug', 'production', 'staging', 'development'
 * );
 * $environmentFilter = new InArray('environments', $configSubsetValue);
 * $result = $environmentFilter->withValue(['debug', 'staging']); // Valid environments
 * $result = $environmentFilter->withValue(['debug', 'hacking']); // Invalid - 'hacking' not valid
 * ```
 * ```
 * // Validation examples
 * $subsetValue = new SubsetValue(new StringValue(), 'red', 'blue', 'green');
 *
 * // Valid inputs (all elements in enum)
 * $subsetValue->accepts('red');                    // true - single valid element
 * $subsetValue->accepts(['red']);                  // true - array with valid element
 * $subsetValue->accepts(['red', 'blue']);          // true - all elements valid
 * $subsetValue->accepts(['blue', 'green']);        // true - all elements valid
 * $subsetValue->accepts(['red', 'blue', 'green']); // true - all elements valid
 *
 * // Invalid inputs (contains invalid elements)
 * $subsetValue->accepts(['red', 'yellow']);        // false - 'yellow' not in enum
 * $subsetValue->accepts(['purple']);               // false - 'purple' not in enum
 * $subsetValue->accepts(['red', 'blue', 'purple']); // false - 'purple' invalid
 * $subsetValue->accepts([]);                       // false - empty array
 * ```
 *
 * Important notes:
 * - ALL array elements must exist in the enum set
 * - More restrictive than IntersectValue (which allows partial matches)
 * - Single values are automatically converted to arrays
 * - Empty arrays are rejected (considered invalid)
 * - Useful for strict validation where no invalid values are allowed
 * - Perfect for permission systems and configuration validation
 * - Maintains order of elements in the result
 * - Based on EnumValue for individual element validation
 */
final class SubsetValue implements ValueInterface
{
    private readonly ValueInterface $enum;

    public function __construct(ValueInterface $enum, mixed ...$values)
    {
        $this->enum = new EnumValue($enum, ...$values);
    }

    public function accepts(mixed $value): bool
    {
        $value = (array) $value;

        return match (true) {
            \count($value) === 1 => $this->enum->accepts(\current($value)),
            empty($value) => false,
            default => $this->arrayType()->accepts($value),
        };
    }

    public function convert(mixed $value): array
    {
        return $this->arrayType()->convert((array) $value);
    }

    private function arrayType(): ArrayValue
    {
        return new ArrayValue($this->enum);
    }
}
