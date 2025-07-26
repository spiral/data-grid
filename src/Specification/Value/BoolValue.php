<?php

declare(strict_types=1);

namespace Spiral\DataGrid\Specification\Value;

use Spiral\DataGrid\Exception\ValueException;
use Spiral\DataGrid\Specification\ValueInterface;

/**
 * Validates and converts boolean values from various input formats.
 * Accepts actual booleans, numeric strings ('0', '1'), and text representations ('true', 'false').
 *
 * Real-world usage examples:
 * - Feature toggles: Enable/disable application features based on user input
 * - Form checkboxes: Process HTML form checkbox values (checked/unchecked)
 * - API flags: Handle boolean parameters in REST APIs and configuration
 * - User preferences: Store user settings like notifications on/off, dark mode, etc.
 * - Content filtering: Published/unpublished, active/inactive, visible/hidden states
 * - System configuration: Debug mode, maintenance mode, feature flags
 * - Permission systems: Allow/deny access, read-only mode, admin privileges
 * - E-commerce: In stock, on sale, featured product flags
 *
 * Accepted input formats:
 * - Actual booleans: true, false
 * - Numeric strings: '0' (false), '1' (true)
 * - Text strings: 'true' (true), 'false' (false) - case insensitive
 * - Numeric values: 0 (false), 1 (true)
 *
 * @example
 * // Feature toggle filter
 * $featureFilter = new Equals('feature_enabled', new BoolValue());
 * $result = $featureFilter->withValue('true'); // Enables feature
 * $result = $featureFilter->withValue('0'); // Disables feature
 *
 * @example
 * // HTML form checkbox processing
 * $publishedValue = new BoolValue();
 * // Checkbox checked: value = '1' or 'on'
 * // Checkbox unchecked: value not present or '0'
 * $publishedValue->accepts('1'); // true
 * $publishedValue->convert('1'); // Returns true
 * $publishedValue->accepts('0'); // true
 * $publishedValue->convert('0'); // Returns false
 *
 * @example
 * // User preference settings
 * $notificationsValue = new BoolValue();
 * $preferencesFilter = new Equals('email_notifications', $notificationsValue);
 * $result = $preferencesFilter->withValue('true'); // Enable notifications
 * $result = $preferencesFilter->withValue('false'); // Disable notifications
 *
 * @example
 * // API boolean parameters
 * $activeValue = new BoolValue();
 * // GET /api/users?active=1 (show only active users)
 * // GET /api/users?active=0 (show only inactive users)
 * $userFilter = new Equals('is_active', $activeValue);
 * $result = $userFilter->withValue($_GET['active']);
 *
 * @example
 * // Content management system
 * $featuredValue = new BoolValue();
 * $contentFilter = new Equals('is_featured', $featuredValue);
 * $result = $contentFilter->withValue('1'); // Mark as featured
 * $result = $contentFilter->withValue('false'); // Remove featured status
 *
 * @example
 * // E-commerce product flags
 * $onSaleValue = new BoolValue();
 * $productFilter = new Equals('on_sale', $onSaleValue);
 * $result = $productFilter->withValue(true); // Product on sale
 * $result = $productFilter->withValue('0'); // Regular price
 *
 * @example
 * // System configuration
 * $debugValue = new BoolValue();
 * $configFilter = new Equals('debug_mode', $debugValue);
 * $result = $configFilter->withValue('TRUE'); // Enable debug (case insensitive)
 * $result = $configFilter->withValue('FALSE'); // Disable debug
 *
 * @example
 * // Permission and access control
 * $adminValue = new BoolValue();
 * $permissionFilter = new Equals('is_admin', $adminValue);
 * $result = $permissionFilter->withValue(1); // Grant admin access
 * $result = $permissionFilter->withValue(0); // Regular user access
 *
 * @example
 * // Form validation examples
 * $boolValue = new BoolValue();
 *
 * // Valid inputs
 * $boolValue->accepts(true);      // true
 * $boolValue->accepts(false);     // true
 * $boolValue->accepts('1');       // true
 * $boolValue->accepts('0');       // true
 * $boolValue->accepts('true');    // true
 * $boolValue->accepts('false');   // true
 * $boolValue->accepts('TRUE');    // true (case insensitive)
 * $boolValue->accepts(1);         // true
 * $boolValue->accepts(0);         // true
 *
 * // Invalid inputs
 * $boolValue->accepts('yes');     // false
 * $boolValue->accepts('no');      // false
 * $boolValue->accepts('on');      // false
 * $boolValue->accepts('off');     // false
 * $boolValue->accepts(2);         // false
 * $boolValue->accepts([]);        // false
 * $boolValue->accepts(null);      // false
 *
 * @example
 * // Conversion examples
 * $boolValue = new BoolValue();
 *
 * $boolValue->convert('1');       // Returns true
 * $boolValue->convert('0');       // Returns false
 * $boolValue->convert('true');    // Returns true
 * $boolValue->convert('false');   // Returns false
 * $boolValue->convert('TRUE');    // Returns true
 * $boolValue->convert('FALSE');   // Returns false
 * $boolValue->convert(true);      // Returns true
 * $boolValue->convert(false);     // Returns false
 * $boolValue->convert(1);         // Returns true
 * $boolValue->convert(0);         // Returns false
 *
 * @example
 * // Complex boolean filtering
 * $multipleFlags = new Map([
 *     'published' => new Equals('is_published', new BoolValue()),
 *     'featured' => new Equals('is_featured', new BoolValue()),
 *     'archived' => new Equals('is_archived', new BoolValue())
 * ]);
 * $result = $multipleFlags->withValue([
 *     'published' => '1',    // true
 *     'featured' => 'false', // false
 *     'archived' => '0'      // false
 * ]);
 *
 * Note: Always call accepts() before convert() to ensure the value can be properly converted.
 * Invalid values passed to convert() will throw a ValueException.
 */
final class BoolValue implements ValueInterface
{
    public function accepts(mixed $value): bool
    {
        if (\is_bool($value)) {
            return true;
        }

        if (\is_scalar($value)) {
            return \in_array(\strtolower((string) $value), ['0', '1', 'true', 'false'], true);
        }

        return false;
    }

    public function convert(mixed $value): bool
    {
        if (\is_bool($value)) {
            return $value;
        }

        if (\is_scalar($value)) {
            return match (\strtolower((string) $value)) {
                '0', 'false' => false,
                '1', 'true' => true,
            };
        }

        throw new ValueException(
            \sprintf(
                'Value is expected to be boolean, got `%s`. Check the value with `accepts()` method first.',
                \get_debug_type($value),
            ),
        );
    }
}
