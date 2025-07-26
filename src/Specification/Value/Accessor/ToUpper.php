<?php

declare(strict_types=1);

namespace Spiral\DataGrid\Specification\Value\Accessor;

/**
 * String uppercase conversion accessor that transforms strings to uppercase before validation.
 * Useful for data standardization, constant values, and ensuring consistent formatting.
 *
 * Real-world usage examples:
 * - Configuration constants: Convert config values to uppercase (DEBUG, PRODUCTION)
 * - Status codes: Standardize status values (ACTIVE, INACTIVE, PENDING)
 * - Country codes: Normalize country codes to uppercase (US, UK, DE)
 * - Currency codes: Standardize currency codes (USD, EUR, GBP)
 * - Database constants: Ensure uppercase for database enum values
 * - API responses: Standardize response codes and status values
 * - System identifiers: Format system codes and identifiers
 * - Protocol standards: HTTP methods, SQL keywords, etc.
 *
 * Benefits:
 * - Consistent formatting for constants and codes
 * - Better readability for system values
 * - Compliance with standards (ISO codes, RFC standards)
 * - Improved data consistency in databases
 * - Professional appearance in APIs and logs
 *
 * @example
 * // Basic uppercase conversion
 * $uppercaseString = new ToUpper(new StringValue());
 * $statusFilter = new Equals('status', $uppercaseString);
 * $result = $statusFilter->withValue('active'); // Converts to 'ACTIVE'
 *
 * @example
 * // Configuration value processing
 * $configProcessor = new ToUpper(
 *     new EnumValue(new StringValue(), 'DEBUG', 'INFO', 'WARNING', 'ERROR')
 * );
 * $configFilter = new Equals('log_level', $configProcessor);
 * $result = $configFilter->withValue('debug'); // Converts to 'DEBUG'
 *
 * @example
 * // Country code standardization
 * $countryProcessor = new ToUpper(
 *     new Trim(
 *         new RegexValue('/^[A-Z]{2}$/')
 *     )
 * );
 * $countryFilter = new Equals('country_code', $countryProcessor);
 * $result = $countryFilter->withValue('  us  '); // Converts to 'US'
 *
 * @example
 * // Currency code processing
 * $currencyProcessor = new ToUpper(
 *     new EnumValue(new StringValue(), 'USD', 'EUR', 'GBP', 'JPY', 'CAD')
 * );
 * $currencyFilter = new Equals('currency', $currencyProcessor);
 * $result = $currencyFilter->withValue('usd'); // Converts to 'USD'
 *
 * @example
 * // Status value standardization
 * $statusProcessor = new ToUpper(
 *     new EnumValue(new StringValue(), 'ACTIVE', 'INACTIVE', 'PENDING', 'SUSPENDED')
 * );
 * $statusFilter = new Equals('account_status', $statusProcessor);
 * $result = $statusFilter->withValue('active'); // Converts to 'ACTIVE'
 *
 * @example
 * // HTTP method validation
 * $methodProcessor = new ToUpper(
 *     new EnumValue(new StringValue(), 'GET', 'POST', 'PUT', 'DELETE', 'PATCH')
 * );
 * $methodFilter = new Equals('http_method', $methodProcessor);
 * $result = $methodFilter->withValue('get'); // Converts to 'GET'
 *
 * @example
 * // Database column name standardization
 * $columnProcessor = new ToUpper(
 *     new RegexValue('/^[A-Z_]+$/')
 * );
 * $columnFilter = new Equals('column_name', $columnProcessor);
 * $result = $columnFilter->withValue('user_name'); // Converts to 'USER_NAME'
 *
 * @example
 * // API response code processing
 * $responseProcessor = new ToUpper(
 *     new EnumValue(new StringValue(), 'SUCCESS', 'ERROR', 'WARNING', 'INFO')
 * );
 * $responseFilter = new Equals('response_type', $responseProcessor);
 * $result = $responseFilter->withValue('success'); // Converts to 'SUCCESS'
 *
 * @example
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
 *
 * @example
 * // Conversion examples
 * $uppercase = new ToUpper(new StringValue());
 *
 * $uppercase->convert('hello');         // Returns 'HELLO'
 * $uppercase->convert('MixedCase');     // Returns 'MIXEDCASE'
 * $uppercase->convert('already-upper'); // Returns 'ALREADY-UPPER'
 * $uppercase->convert('123');           // Returns '123'
 * $uppercase->convert('');              // Returns ''
 *
 * @example
 * // System configuration processing
 * $envProcessor = new ToUpper(
 *     new EnumValue(new StringValue(), 'DEVELOPMENT', 'STAGING', 'PRODUCTION')
 * );
 * $envFilter = new Equals('environment', $envProcessor);
 * $result = $envFilter->withValue('production'); // Converts to 'PRODUCTION'
 *
 * @example
 * // Language code standardization
 * $langProcessor = new ToUpper(
 *     new RegexValue('/^[A-Z]{2}$/')
 * );
 * $langFilter = new Equals('language_code', $langProcessor);
 * $result = $langFilter->withValue('en'); // Converts to 'EN'
 *
 * @example
 * // Database enum processing
 * $enumProcessor = new ToUpper(
 *     new EnumValue(new StringValue(), 'MALE', 'FEMALE', 'OTHER')
 * );
 * $genderFilter = new Equals('gender', $enumProcessor);
 * $result = $genderFilter->withValue('male'); // Converts to 'MALE'
 *
 * @example
 * // File type standardization
 * $fileTypeProcessor = new Split(
 *     new ArrayValue(
 *         new ToUpper(
 *             new EnumValue(new StringValue(), 'PDF', 'DOC', 'XLS', 'JPG', 'PNG')
 *         )
 *     ),
 *     ','
 * );
 * $result = $fileTypeProcessor->convert('pdf,jpg,png');
 * // Results in: ['PDF', 'JPG', 'PNG']
 *
 * @example
 * // Priority level processing
 * $priorityProcessor = new ToUpper(
 *     new EnumValue(new StringValue(), 'LOW', 'MEDIUM', 'HIGH', 'CRITICAL')
 * );
 * $priorityFilter = new Equals('priority', $priorityProcessor);
 * $result = $priorityFilter->withValue('high'); // Converts to 'HIGH'
 *
 * @example
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
 *
 * @example
 * // API parameter processing
 * $apiProcessor = new ToUpper(new StringValue());
 * // GET /api/data?format=json
 * if ($apiProcessor->accepts($_GET['format'])) {
 *     $format = $apiProcessor->convert($_GET['format']);
 *     // Results in: 'JSON'
 * }
 *
 * @example
 * // Protocol standardization
 * $protocolProcessor = new ToUpper(
 *     new EnumValue(new StringValue(), 'HTTP', 'HTTPS', 'FTP', 'SFTP')
 * );
 * $protocolFilter = new Equals('protocol', $protocolProcessor);
 * $result = $protocolFilter->withValue('https'); // Converts to 'HTTPS'
 *
 * @example
 * // SQL keyword processing
 * $sqlProcessor = new ToUpper(
 *     new EnumValue(new StringValue(), 'SELECT', 'INSERT', 'UPDATE', 'DELETE')
 * );
 * $sqlFilter = new Equals('sql_operation', $sqlProcessor);
 * $result = $sqlFilter->withValue('select'); // Converts to 'SELECT'
 *
 * @example
 * // Day of week standardization
 * $dayProcessor = new ToUpper(
 *     new EnumValue(new StringValue(), 'MONDAY', 'TUESDAY', 'WEDNESDAY', 'THURSDAY', 'FRIDAY', 'SATURDAY', 'SUNDAY')
 * );
 * $dayFilter = new Equals('day_of_week', $dayProcessor);
 * $result = $dayFilter->withValue('monday'); // Converts to 'MONDAY'
 *
 * @example
 * // Form validation with standardization
 * $formProcessor = new ToUpper(
 *     new Trim(new StringValue())
 * );
 * $formFilter = new Map([
 *     'state' => new Equals('state', $formProcessor),        // 'ny' -> 'NY'
 *     'country' => new Equals('country', $formProcessor),    // 'usa' -> 'USA'
 *     'currency' => new Equals('currency', $formProcessor)   // 'usd' -> 'USD'
 * ]);
 *
 * @example
 * // Content type processing
 * $contentTypeProcessor = new ToUpper(
 *     new EnumValue(new StringValue(), 'JSON', 'XML', 'HTML', 'TEXT', 'CSV')
 * );
 * $contentFilter = new Equals('content_type', $contentTypeProcessor);
 * $result = $contentFilter->withValue('json'); // Converts to 'JSON'
 *
 * @example
 * // System log level processing
 * $logProcessor = new ToUpper(
 *     new EnumValue(new StringValue(), 'TRACE', 'DEBUG', 'INFO', 'WARN', 'ERROR', 'FATAL')
 * );
 * $logFilter = new Equals('log_level', $logProcessor);
 * $result = $logFilter->withValue('error'); // Converts to 'ERROR'
 *
 * @example
 * // Database schema processing
 * $schemaProcessor = new ToUpper(new StringValue());
 * $schemaFilter = new All(
 *     new Equals('table_name', $schemaProcessor),
 *     new InArray('column_names', new ArrayValue($schemaProcessor))
 * );
 * // Standardizes database identifiers to uppercase
 *
 * @example
 * // Network protocol processing
 * $networkProcessor = new ToUpper(
 *     new EnumValue(new StringValue(), 'TCP', 'UDP', 'ICMP', 'ARP')
 * );
 * $networkFilter = new Equals('protocol', $networkProcessor);
 * $result = $networkFilter->withValue('tcp'); // Converts to 'TCP'
 *
 * @example
 * // File permission processing
 * $permProcessor = new ToUpper(
 *     new RegexValue('/^[RWX-]{9}$/')
 * );
 * $permFilter = new Equals('permissions', $permProcessor);
 * $result = $permFilter->withValue('rwxr-xr-x'); // Converts to 'RWXR-XR-X'
 *
 * @example
 * // Complex chaining example
 * $complexProcessor = new Split(
 *     new ArrayValue(
 *         new ToUpper(
 *             new Trim(
 *                 new EnumValue(new StringValue(), 'ADMIN', 'USER', 'GUEST', 'MODERATOR')
 *             )
 *         )
 *     ),
 *     ','
 * );
 * $result = $complexProcessor->convert('  admin  ,  user  ,  guest  ');
 * // Results in: ['ADMIN', 'USER', 'GUEST'] (split, trimmed, uppercased, validated)
 *
 * @example
 * // Data import standardization
 * $importProcessor = new ToUpper(
 *     new Trim(new StringValue(true)) // Allow empty values
 * );
 * $importFilter = new InArray('csv_codes', new ArrayValue($importProcessor));
 * // Standardizes imported codes to uppercase
 *
 * @example
 * // Error handling
 * $safeProcessor = new ToUpper(new StringValue());
 * if ($safeProcessor->accepts($userInput)) {
 *     $standardized = $safeProcessor->convert($userInput);
 *     // Process standardized uppercase string
 * } else {
 *     // Handle non-string input
 * }
 *
 * @example
 * // System constant processing
 * $constantProcessor = new ToUpper(
 *     new RegexValue('/^[A-Z_]+$/')
 * );
 * $constantFilter = new Equals('system_constant', $constantProcessor);
 * $result = $constantFilter->withValue('max_file_size'); // Converts to 'MAX_FILE_SIZE'
 *
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
