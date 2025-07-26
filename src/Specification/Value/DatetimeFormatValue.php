<?php

declare(strict_types=1);

namespace Spiral\DataGrid\Specification\Value;

use Spiral\DataGrid\Specification\ValueInterface;

/**
 * Validates and converts datetime strings using a specific format.
 * Accepts string input that matches the specified format and converts to DateTimeImmutable or formatted string.
 *
 * Real-world usage examples:
 * - Form date inputs: Process HTML date inputs with specific formats (Y-m-d)
 * - API date parameters: Handle standardized date formats in REST APIs
 * - Import/export systems: Parse dates from CSV files, external systems
 * - Localized date formats: Accept different regional date formats
 * - Database date fields: Ensure dates match expected database format
 * - Report date ranges: Validate start/end dates in specific formats
 * - Event scheduling: Process event dates in required format
 * - Data migration: Convert dates between different system formats
 *
 * Format examples:
 * - 'Y-m-d' for dates like '2024-01-15'
 * - 'Y-m-d H:i:s' for datetimes like '2024-01-15 14:30:00'
 * - 'd/m/Y' for formats like '15/01/2024'
 * - 'M j, Y' for formats like 'Jan 15, 2024'
 * - 'Y-m-d\TH:i:s\Z' for ISO 8601 format
 *
 * @example
 * // HTML date input processing (Y-m-d format)
 * $dateValue = new DatetimeFormatValue('Y-m-d');
 * $dateFilter = new Gte('start_date', $dateValue);
 * $result = $dateFilter->withValue('2024-01-15'); // Valid HTML date input
 *
 * @example
 * // US date format processing (m/d/Y)
 * $usDateValue = new DatetimeFormatValue('m/d/Y');
 * $eventFilter = new Between('event_date', $usDateValue);
 * $result = $eventFilter->withValue('01/15/2024'); // January 15, 2024
 *
 * @example
 * // API datetime with conversion to different format
 * $apiDateValue = new DatetimeFormatValue('Y-m-d H:i:s', 'Y-m-d');
 * $logFilter = new Equals('created_at', $apiDateValue);
 * $result = $logFilter->withValue('2024-01-15 14:30:00');
 * // Converts to '2024-01-15' (date only)
 *
 * @example
 * // European date format (d.m.Y)
 * $europeanDateValue = new DatetimeFormatValue('d.m.Y');
 * $orderFilter = new Gte('order_date', $europeanDateValue);
 * $result = $orderFilter->withValue('15.01.2024'); // 15th January 2024
 *
 * @example
 * // Time-only format (H:i:s)
 * $timeValue = new DatetimeFormatValue('H:i:s');
 * $scheduleFilter = new Between('start_time', $timeValue);
 * $result = $scheduleFilter->withValue('14:30:00'); // 2:30 PM
 *
 * @example
 * // ISO 8601 format processing
 * $isoDateValue = new DatetimeFormatValue('Y-m-d\TH:i:s\Z');
 * $apiFilter = new Gte('timestamp', $isoDateValue);
 * $result = $apiFilter->withValue('2024-01-15T14:30:00Z');
 *
 * @example
 * // Month/Year format for reports
 * $monthYearValue = new DatetimeFormatValue('Y-m');
 * $reportFilter = new Equals('report_month', $monthYearValue);
 * $result = $reportFilter->withValue('2024-01'); // January 2024
 *
 * @example
 * // Custom readable format
 * $readableValue = new DatetimeFormatValue('F j, Y');
 * $newsFilter = new Gte('publish_date', $readableValue);
 * $result = $newsFilter->withValue('January 15, 2024');
 *
 * @example
 * // Format validation examples
 * $strictDateValue = new DatetimeFormatValue('Y-m-d');
 *
 * $strictDateValue->accepts('2024-01-15');    // true - exact format match
 * $strictDateValue->accepts('2024-1-15');     // false - missing leading zero
 * $strictDateValue->accepts('01/15/2024');    // false - different format
 * $strictDateValue->accepts('2024-01-15 10:30'); // false - extra time part
 * $strictDateValue->accepts('invalid-date');   // false - invalid date
 *
 * @example
 * // Conversion with output format
 * $convertingValue = new DatetimeFormatValue('Y-m-d', 'F j, Y');
 * $convertingValue->convert('2024-01-15'); // Returns 'January 15, 2024'
 *
 * // Conversion to DateTimeImmutable (no output format)
 * $datetimeValue = new DatetimeFormatValue('Y-m-d');
 * $datetimeValue->convert('2024-01-15'); // Returns DateTimeImmutable object
 *
 * @example
 * // Form processing with strict validation
 * $formDateValue = new DatetimeFormatValue('Y-m-d');
 * if ($formDateValue->accepts($_POST['birth_date'])) {
 *     $birthDate = $formDateValue->convert($_POST['birth_date']);
 *     // Process valid date
 * } else {
 *     // Handle invalid date format
 * }
 *
 * @example
 * // Data import validation
 * $csvDateValue = new DatetimeFormatValue('d/m/Y', 'Y-m-d');
 * // Convert CSV format (15/01/2024) to database format (2024-01-15)
 * foreach ($csvRows as $row) {
 *     if ($csvDateValue->accepts($row['date'])) {
 *         $dbDate = $csvDateValue->convert($row['date']);
 *         // Store in database with standardized format
 *     }
 * }
 *
 * @example
 * // Multiple format validation (use Select filter)
 * $multiFormatSelect = new Select([
 *     'iso' => new Equals('date', new DatetimeFormatValue('Y-m-d')),
 *     'us' => new Equals('date', new DatetimeFormatValue('m/d/Y')),
 *     'european' => new Equals('date', new DatetimeFormatValue('d.m.Y'))
 * ]);
 * // Accepts multiple date formats
 *
 * @example
 * // Time zone aware processing
 * $timezoneValue = new DatetimeFormatValue('Y-m-d H:i:s T');
 * $result = $timezoneValue->withValue('2024-01-15 14:30:00 EST');
 *
 * Important notes:
 * - Format must match exactly - no partial matches
 * - Invalid dates (like February 30th) will be rejected
 * - Returns null for invalid input (check with accepts() first)
 * - Without output format, returns DateTimeImmutable object
 * - With output format, returns formatted string
 * - Uses PHP's DateTime format codes (see PHP documentation)
 */
final class DatetimeFormatValue implements ValueInterface
{
    public function __construct(
        private readonly string $readFrom,
        private readonly ?string $convertInto = null,
    ) {}

    public function accepts(mixed $value): bool
    {
        return \is_string($value) && $this->convert($value) !== null;
    }

    public function convert(mixed $value): string|null|\DateTimeInterface
    {
        try {
            $datetime = \DateTimeImmutable::createFromFormat($this->readFrom, (string) $value);
            if (!$datetime instanceof \DateTimeImmutable) {
                return null;
            }

            if ($this->convertInto !== null) {
                $formatted = $datetime->format($this->convertInto);
                return \is_string($formatted) ? $formatted : null;
            }

            return $datetime;
        } catch (\Throwable) {
            return null;
        }
    }
}
