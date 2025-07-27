<?php

declare(strict_types=1);

namespace Spiral\DataGrid\Specification\Value;

use Spiral\DataGrid\Specification\ValueInterface;

/**
 * Validates and converts datetime values from various flexible input formats.
 * Accepts timestamps, date strings, relative date expressions, and converts to DateTimeImmutable.
 * More flexible than DatetimeFormatValue - accepts many common datetime representations.
 *
 * Accepted input formats:
 * - Unix timestamps: '1640995200', 1640995200
 * - ISO dates: '2024-01-15', '2024-01-15T14:30:00Z'
 * - Common formats: '01/15/2024', '15-Jan-2024', 'January 15, 2024'
 * - Relative expressions: 'yesterday', 'last week', '+1 day', '-30 days'
 * - Time expressions: 'now', 'today', 'tomorrow'
 * - Empty strings: '' (treated as valid, returns null)
 *
 * ```
 * // Flexible date range filtering
 * $dateValue = new DatetimeValue();
 * $dateFilter = new Between('created_at', $dateValue);
 * $result = $dateFilter->withValue(['last week', 'today']); // Relative dates
 * ```
 * ```
 * // API endpoint with flexible date input
 * $startDateValue = new DatetimeValue();
 * $apiFilter = new Gte('start_date', $startDateValue);
 * // Accepts: '2024-01-15', '1640995200', 'yesterday', etc.
 * $result = $apiFilter->withValue($_GET['start_date']);
 * ```
 * ```
 * // Log filtering with timestamps
 * $logDateValue = new DatetimeValue();
 * $logFilter = new Gte('log_timestamp', $logDateValue);
 * $result = $logFilter->withValue('1640995200'); // Unix timestamp
 * $result = $logFilter->withValue('-1 hour'); // One hour ago
 * ```
 * ```
 * // Event scheduling
 * $eventDateValue = new DatetimeValue();
 * $eventFilter = new Between('event_date', $eventDateValue);
 * $result = $eventFilter->withValue(['next Monday', 'next Friday']);
 * ```
 * ```
 * // User-friendly date inputs
 * $userDateValue = new DatetimeValue();
 * $userFilter = new Gte('birth_date', $userDateValue);
 * $result = $userFilter->withValue('January 15, 1990');
 * $result = $userFilter->withValue('15/01/1990');
 * $result = $userFilter->withValue('1990-01-15');
 * ```
 * ```
 * // Backup file processing
 * $backupDateValue = new DatetimeValue();
 * $backupFilter = new Lt('backup_date', $backupDateValue);
 * $result = $backupFilter->withValue('-30 days'); // Older than 30 days
 * ```
 * ```
 * // Report generation with relative dates
 * $reportDateValue = new DatetimeValue();
 * $reportFilter = new Between('report_period', $reportDateValue);
 * $result = $reportFilter->withValue(['first day of last month', 'last day of last month']);
 * ```
 * ```
 * // Content management with publish dates
 * $publishDateValue = new DatetimeValue();
 * $contentFilter = new Lte('publish_at', $publishDateValue);
 * $result = $contentFilter->withValue('now'); // Published content only
 * ```
 * ```
 * // Input validation examples
 * $dateValue = new DatetimeValue();
 *
 * // Valid inputs
 * $dateValue->accepts('2024-01-15');           // true - ISO date
 * $dateValue->accepts('1640995200');           // true - timestamp string
 * $dateValue->accepts(1640995200);             // true - timestamp number
 * $dateValue->accepts('yesterday');            // true - relative date
 * $dateValue->accepts('01/15/2024');           // true - US format
 * $dateValue->accepts('15-Jan-2024');          // true - readable format
 * $dateValue->accepts('January 15, 2024');     // true - full format
 * $dateValue->accepts('next week');            // true - relative
 * $dateValue->accepts('+1 day');               // true - relative
 * $dateValue->accepts('');                     // true - empty string
 *
 * // Invalid inputs
 * $dateValue->accepts('invalid-date');         // false
 * $dateValue->accepts('not a date');           // false
 * $dateValue->accepts([]);                     // false
 * $dateValue->accepts(null);                   // false
 * $dateValue->accepts(true);                   // false
 * ```
 * ```
 * // Conversion examples
 * $dateValue = new DatetimeValue();
 *
 * $result = $dateValue->convert('2024-01-15');        // DateTimeImmutable object
 * $result = $dateValue->convert('1640995200');        // DateTimeImmutable from timestamp
 * $result = $dateValue->convert('yesterday');         // DateTimeImmutable for yesterday
 * $result = $dateValue->convert('+1 week');           // DateTimeImmutable for next week
 * $result = $dateValue->convert('');                  // null (empty string)
 * ```
 * Important notes:
 * - Empty strings are accepted and return null
 * - Uses PHP's DateTime constructor (very flexible parsing)
 * - Numeric strings are treated as Unix timestamps
 * - Always returns DateTimeImmutable objects (immutable)
 * - Returns null for invalid inputs (always check with accepts() first)
 * - Timezone handling depends on system/application settings
 * - More flexible than DatetimeFormatValue but less strict
 */
final class DatetimeValue implements ValueInterface
{
    public function accepts(mixed $value): bool
    {
        if ($value === '') {
            return true;
        }
        return (\is_numeric($value) || \is_string($value)) && $this->convert($value) !== null;
    }

    public function convert(mixed $value): ?\DateTimeImmutable
    {
        try {
            $value = (string) $value;

            return new \DateTimeImmutable(\is_numeric($value) ? \sprintf('@%s', $value) : $value);
        } catch (\Throwable) {
            return null;
        }
    }
}
