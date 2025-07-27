<?php

declare(strict_types=1);

namespace Spiral\DataGrid\Specification\Value\Accessor;

/**
 * String uppercase conversion accessor that transforms strings to uppercase before validation.
 * Useful for data standardization, constant values, and ensuring consistent formatting.
 *
 * ```
 * // Basic uppercase conversion
 * $uppercaseString = new ToUpper(new StringValue());
 * $statusFilter = new Equals('status', $uppercaseString);
 * $result = $statusFilter->withValue('active'); // Converts to 'ACTIVE'
 * ```
 * ```
 * // Configuration value processing
 * $configProcessor = new ToUpper(
 *     new EnumValue(new StringValue(), 'DEBUG', 'INFO', 'WARNING', 'ERROR')
 * );
 * $configFilter = new Equals('log_level', $configProcessor);
 * $result = $configFilter->withValue('debug'); // Converts to 'DEBUG'
 * ```
 * ```
 * // Country code standardization
 * $countryProcessor = new ToUpper(
 *     new Trim(
 *         new RegexValue('/^[A-Z]{2}$/')
 *     )
 * );
 * $countryFilter = new Equals('country_code', $countryProcessor);
 * $result = $countryFilter->withValue('  us  '); // Converts to 'US'
 * ```
 * ```
 * // Validation examples
 * $uppercase = new ToUpper(new StringValue());
 *
 * // Valid inputs (strings that can be converted)
 * $uppercase->accepts('hello');         // true - string
 * $uppercase->accepts('MixedCase');     // true - string
 * $uppercase->accepts('ALREADY-UPPER'); // true - string
 * $uppercase->accepts('');              // depends on StringValue config
 *
 * // Inputs handled by next in chain
 * $uppercase->accepts(123);             // true - StringValue can convert
 * $uppercase->accepts([]);              // false - StringValue can't handle arrays
 * $uppercase->accepts(null);            // false - StringValue can't handle null
 * ```
 * ```
 * // System configuration processing
 * $envProcessor = new ToUpper(
 *     new EnumValue(new StringValue(), 'DEVELOPMENT', 'STAGING', 'PRODUCTION')
 * );
 * $envFilter = new Equals('environment', $envProcessor);
 * $result = $envFilter->withValue('production'); // Converts to 'PRODUCTION'
 * ```
 * ```
 * // Language code standardization
 * $langProcessor = new ToUpper(
 *     new RegexValue('/^[A-Z]{2}$/')
 * );
 * $langFilter = new Equals('language_code', $langProcessor);
 * $result = $langFilter->withValue('en'); // Converts to 'EN'
 * ```
 * ```
 * // Permission level processing
 * $permissionProcessor = new Split(
 *     new ArrayValue(
 *         new ToUpper(
 *             new EnumValue(new StringValue(), 'READ', 'WRITE', 'DELETE', 'ADMIN')
 *         )
 *     ),
 *     ','
 * );
 * $result = $permissionProcessor->convert('read,write,admin');
 * // Results in: ['READ', 'WRITE', 'ADMIN']
 * ```
 * ```
 * // Protocol standardization
 * $protocolProcessor = new ToUpper(
 *     new EnumValue(new StringValue(), 'HTTP', 'HTTPS', 'FTP', 'SFTP')
 * );
 * $protocolFilter = new Equals('protocol', $protocolProcessor);
 * $result = $protocolFilter->withValue('https'); // Converts to 'HTTPS'
 * ```
 * ```
 * // SQL keyword processing
 * $sqlProcessor = new ToUpper(
 *     new EnumValue(new StringValue(), 'SELECT', 'INSERT', 'UPDATE', 'DELETE')
 * );
 * $sqlFilter = new Equals('sql_operation', $sqlProcessor);
 * $result = $sqlFilter->withValue('select'); // Converts to 'SELECT'
 * ```
 * ```
 * // Content type processing
 * $contentTypeProcessor = new ToUpper(
 *     new EnumValue(new StringValue(), 'JSON', 'XML', 'HTML', 'TEXT', 'CSV')
 * );
 * $contentFilter = new Equals('content_type', $contentTypeProcessor);
 * $result = $contentFilter->withValue('json'); // Converts to 'JSON'
 * ```
 * ```
 * // System log level processing
 * $logProcessor = new ToUpper(
 *     new EnumValue(new StringValue(), 'TRACE', 'DEBUG', 'INFO', 'WARN', 'ERROR', 'FATAL')
 * );
 * $logFilter = new Equals('log_level', $logProcessor);
 * $result = $logFilter->withValue('error'); // Converts to 'ERROR'
 * ```
 * ```
 * // Database schema processing
 * $schemaProcessor = new ToUpper(new StringValue());
 * $schemaFilter = new All(
 *     new Equals('table_name', $schemaProcessor),
 *     new InArray('column_names', new ArrayValue($schemaProcessor))
 * );
 * // Standardizes database identifiers to uppercase
 * ```
 * ```
 * // Network protocol processing
 * $networkProcessor = new ToUpper(
 *     new EnumValue(new StringValue(), 'TCP', 'UDP', 'ICMP', 'ARP')
 * );
 * $networkFilter = new Equals('protocol', $networkProcessor);
 * $result = $networkFilter->withValue('tcp'); // Converts to 'TCP'
 * ```
 * Important notes:
 * - Only processes string values directly
 * - Uses PHP's strtoupper() function
 * - Preserves non-alphabetic characters unchanged
 * - Useful for standardizing constants and codes
 * - Can be chained with other accessors
 * - Essential for data consistency and professional formatting
 * - Helps maintain standards compliance (ISO codes, RFC standards)
 * - Improves readability of system values and constants
 */
final class ToUpper extends Accessor
{
    protected function acceptsCurrent(mixed $value): bool
    {
        return \is_string($value);
    }

    protected function convertCurrent(mixed $value): mixed
    {
        return \is_string($value) ? \strtoupper($value) : $value;
    }
}
