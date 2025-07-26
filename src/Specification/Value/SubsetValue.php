<?php

declare(strict_types=1);

namespace Spiral\DataGrid\Specification\Value;

use Spiral\DataGrid\Specification\ValueInterface;

/**
 * Validates arrays where ALL elements must exist in the predefined enum set.
 * This is the opposite of IntersectValue - requires complete subset matching rather than partial.
 *
 * Real-world usage examples:
 * - Permission validation: User must have ALL required permissions from valid set
 * - Skill requirements: Candidate must have ALL required skills from approved list
 * - Feature validation: Product must support ALL requested features from available options
 * - Tag validation: Content must use only approved tags (no invalid tags allowed)
 * - Category restrictions: Items must belong to valid categories only
 * - Language validation: Content must be in supported languages only
 * - Role validation: User roles must be from predefined valid roles
 * - Configuration validation: All settings must be from allowed values
 *
 * Difference from IntersectValue:
 * - SubsetValue: ALL elements must be valid (strict validation)
 * - IntersectValue: At least one element must be valid (permissive validation)
 *
 * @example
 * // Permission system (user must have only valid permissions)
 * $permissionSubsetValue = new SubsetValue(
 *     new StringValue(),
 *     'read', 'write', 'admin', 'moderate', 'delete'
 * );
 * $userFilter = new InArray('permissions', $permissionSubsetValue);
 * $result = $userFilter->withValue(['read', 'write']);        // Valid - all permissions valid
 * $result = $userFilter->withValue(['read', 'invalid']);      // Invalid - contains invalid permission
 *
 * @example
 * // Required skills validation (all must be from approved list)
 * $skillSubsetValue = new SubsetValue(
 *     new StringValue(),
 *     'php', 'javascript', 'python', 'react', 'vue', 'mysql'
 * );
 * $jobFilter = new InArray('required_skills', $skillSubsetValue);
 * $result = $jobFilter->withValue(['php', 'mysql']);          // Valid - both skills approved
 * $result = $jobFilter->withValue(['php', 'cobol']);          // Invalid - 'cobol' not approved
 *
 * @example
 * // Product feature validation
 * $featureSubsetValue = new SubsetValue(
 *     new StringValue(),
 *     'bluetooth', 'wifi', 'gps', 'camera', 'nfc', '5g'
 * );
 * $deviceFilter = new InArray('supported_features', $featureSubsetValue);
 * $result = $deviceFilter->withValue(['bluetooth', 'wifi']);  // Valid - all features supported
 * $result = $deviceFilter->withValue(['wifi', 'hologram']);   // Invalid - 'hologram' not supported
 *
 * @example
 * // Content tag validation (only approved tags allowed)
 * $tagSubsetValue = new SubsetValue(
 *     new StringValue(),
 *     'programming', 'tutorial', 'beginner', 'advanced', 'php', 'javascript'
 * );
 * $contentFilter = new InArray('tags', $tagSubsetValue);
 * $result = $contentFilter->withValue(['php', 'tutorial']);   // Valid - approved tags
 * $result = $contentFilter->withValue(['php', 'spam']);       // Invalid - 'spam' not approved
 *
 * @example
 * // User role validation
 * $roleSubsetValue = new SubsetValue(
 *     new StringValue(),
 *     'admin', 'editor', 'author', 'contributor', 'subscriber'
 * );
 * $userFilter = new InArray('roles', $roleSubsetValue);
 * $result = $userFilter->withValue(['editor', 'author']);     // Valid - valid roles
 * $result = $userFilter->withValue(['admin', 'hacker']);      // Invalid - 'hacker' not valid role
 *
 * @example
 * // Language support validation
 * $languageSubsetValue = new SubsetValue(
 *     new StringValue(),
 *     'en', 'es', 'fr', 'de', 'it', 'pt', 'zh', 'ja'
 * );
 * $contentFilter = new InArray('languages', $languageSubsetValue);
 * $result = $contentFilter->withValue(['en', 'es']);          // Valid - supported languages
 * $result = $contentFilter->withValue(['en', 'klingon']);     // Invalid - 'klingon' not supported
 *
 * @example
 * // Category validation (must be from valid categories)
 * $categorySubsetValue = new SubsetValue(
 *     new StringValue(),
 *     'electronics', 'clothing', 'books', 'sports', 'home', 'garden'
 * );
 * $productFilter = new InArray('categories', $categorySubsetValue);
 * $result = $productFilter->withValue(['electronics', 'home']); // Valid categories
 * $result = $productFilter->withValue(['books', 'weapons']);    // Invalid - 'weapons' not allowed
 *
 * @example
 * // Configuration validation (all settings must be valid)
 * $configSubsetValue = new SubsetValue(
 *     new StringValue(),
 *     'debug', 'production', 'staging', 'development'
 * );
 * $environmentFilter = new InArray('environments', $configSubsetValue);
 * $result = $environmentFilter->withValue(['debug', 'staging']); // Valid environments
 * $result = $environmentFilter->withValue(['debug', 'hacking']); // Invalid - 'hacking' not valid
 *
 * @example
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
 *
 * @example
 * // Conversion examples (only valid elements returned)
 * $subsetValue = new SubsetValue(new StringValue(), 'a', 'b', 'c');
 *
 * $subsetValue->convert('b');           // Returns ['b']
 * $subsetValue->convert(['a', 'c']);    // Returns ['a', 'c']
 * $subsetValue->convert(['a', 'b', 'c']); // Returns ['a', 'b', 'c']
 *
 * @example
 * // API access control
 * $apiScopeSubsetValue = new SubsetValue(
 *     new StringValue(),
 *     'read:users', 'write:users', 'read:orders', 'write:orders', 'admin:all'
 * );
 * $tokenFilter = new InArray('token_scopes', $apiScopeSubsetValue);
 * $result = $tokenFilter->withValue(['read:users', 'read:orders']); // Valid scopes
 * $result = $tokenFilter->withValue(['read:users', 'hack:system']); // Invalid - unauthorized scope
 *
 * @example
 * // E-commerce attribute validation
 * $sizeSubsetValue = new SubsetValue(
 *     new StringValue(),
 *     'xs', 's', 'm', 'l', 'xl', 'xxl'
 * );
 * $clothingFilter = new InArray('available_sizes', $sizeSubsetValue);
 * $result = $clothingFilter->withValue(['s', 'm', 'l']);    // Valid sizes
 * $result = $clothingFilter->withValue(['m', 'xxxl']);      // Invalid - 'xxxl' not valid size
 *
 * @example
 * // Social media platform validation
 * $platformSubsetValue = new SubsetValue(
 *     new StringValue(),
 *     'facebook', 'twitter', 'instagram', 'linkedin', 'youtube'
 * );
 * $socialFilter = new InArray('platforms', $platformSubsetValue);
 * $result = $socialFilter->withValue(['facebook', 'twitter']); // Valid platforms
 * $result = $socialFilter->withValue(['twitter', 'myspace']);  // Invalid - 'myspace' not supported
 *
 * @example
 * // Educational course topics
 * $topicSubsetValue = new SubsetValue(
 *     new StringValue(),
 *     'programming', 'data-science', 'machine-learning', 'web-development', 'mobile-development'
 * );
 * $courseFilter = new InArray('topics', $topicSubsetValue);
 * $result = $courseFilter->withValue(['programming', 'web-development']); // Valid topics
 * $result = $courseFilter->withValue(['programming', 'astrology']);       // Invalid - 'astrology' not offered
 *
 * @example
 * // Database user permissions
 * $dbPermissionSubsetValue = new SubsetValue(
 *     new StringValue(),
 *     'SELECT', 'INSERT', 'UPDATE', 'DELETE', 'CREATE', 'DROP', 'ALTER'
 * );
 * $dbUserFilter = new InArray('permissions', $dbPermissionSubsetValue);
 * $result = $dbUserFilter->withValue(['SELECT', 'INSERT']);      // Valid DB permissions
 * $result = $dbUserFilter->withValue(['SELECT', 'SHUTDOWN']);    // Invalid - 'SHUTDOWN' not allowed
 *
 * @example
 * // Form validation with strict requirements
 * $interestSubsetValue = new SubsetValue(
 *     new StringValue(),
 *     'technology', 'science', 'business', 'health', 'entertainment', 'sports'
 * );
 * if ($interestSubsetValue->accepts($_POST['interests'])) {
 *     $validInterests = $interestSubsetValue->convert($_POST['interests']);
 *     // All interests are from approved list
 * } else {
 *     // Some interests are not in approved list
 * }
 *
 * @example
 * // Content moderation system
 * $categorySubsetValue = new SubsetValue(
 *     new StringValue(),
 *     'general', 'technology', 'business', 'education', 'entertainment'
 * );
 * $moderationFilter = new InArray('content_categories', $categorySubsetValue);
 * $result = $moderationFilter->withValue(['technology', 'education']); // Valid categories
 * $result = $moderationFilter->withValue(['technology', 'adult']);     // Invalid - 'adult' not allowed
 *
 * @example
 * // Event planning validation
 * $serviceSubsetValue = new SubsetValue(
 *     new StringValue(),
 *     'catering', 'photography', 'music', 'decoration', 'security', 'transportation'
 * );
 * $eventFilter = new InArray('required_services', $serviceSubsetValue);
 * $result = $eventFilter->withValue(['catering', 'music']);        // Valid services
 * $result = $eventFilter->withValue(['catering', 'fireworks']);    // Invalid - 'fireworks' not offered
 *
 * @example
 * // Software license validation
 * $licenseSubsetValue = new SubsetValue(
 *     new StringValue(),
 *     'MIT', 'Apache-2.0', 'GPL-3.0', 'BSD-3-Clause', 'ISC'
 * );
 * $projectFilter = new InArray('licenses', $licenseSubsetValue);
 * $result = $projectFilter->withValue(['MIT', 'Apache-2.0']);      // Valid licenses
 * $result = $projectFilter->withValue(['MIT', 'Proprietary']);     // Invalid - 'Proprietary' not allowed
 *
 * @example
 * // Quality assurance checklist
 * $checklistSubsetValue = new SubsetValue(
 *     new StringValue(),
 *     'security-review', 'performance-test', 'ui-test', 'integration-test', 'documentation'
 * );
 * $qaFilter = new InArray('completed_checks', $checklistSubsetValue);
 * $result = $qaFilter->withValue(['security-review', 'ui-test']); // Valid QA checks
 * $result = $qaFilter->withValue(['ui-test', 'coffee-break']);    // Invalid - 'coffee-break' not a check
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
