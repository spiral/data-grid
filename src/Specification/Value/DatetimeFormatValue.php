<?php

declare(strict_types=1);

namespace Spiral\DataGrid\Specification\Value;

use Spiral\DataGrid\Specification\ValueInterface;

/**
 * Validates and converts datetime strings using a specific format.
 * Accepts string input that matches the specified format and converts to DateTimeImmutable or formatted string.
 *
 * Format examples:
 * - 'Y-m-d' for dates like '2024-01-15'
 * - 'Y-m-d H:i:s' for datetimes like '2024-01-15 14:30:00'
 * - 'd/m/Y' for formats like '15/01/2024'
 * - 'M j, Y' for formats like 'Jan 15, 2024'
 * - 'Y-m-d\TH:i:s\Z' for ISO 8601 format
 *
 * ```
 * // HTML date input processing (Y-m-d format)
 * $dateValue = new DatetimeFormatValue('Y-m-d');
 * $dateFilter = new Gte('start_date', $dateValue);
 * $result = $dateFilter->withValue('2024-01-15'); // Valid HTML date input
 * ```
 * ```
 * // US date format processing (m/d/Y)
 * $usDateValue = new DatetimeFormatValue('m/d/Y');
 * $eventFilter = new Between('event_date', $usDateValue);
 * $result = $eventFilter->withValue('01/15/2024'); // January 15, 2024
 * ```
 * ```
 * // API datetime with conversion to different format
 * $apiDateValue = new DatetimeFormatValue('Y-m-d H:i:s', 'Y-m-d');
 * $logFilter = new Equals('created_at', $apiDateValue);
 * $result = $logFilter->withValue('2024-01-15 14:30:00');
 * // Converts to '2024-01-15' (date only)
 * ```
 * ```
 * // European date format (d.m.Y)
 * $europeanDateValue = new DatetimeFormatValue('d.m.Y');
 * $orderFilter = new Gte('order_date', $europeanDateValue);
 * $result = $orderFilter->withValue('15.01.2024'); // 15th January 2024
 * ```
 * ```
 * // Time-only format (H:i:s)
 * $timeValue = new DatetimeFormatValue('H:i:s');
 * $scheduleFilter = new Between('start_time', $timeValue);
 * $result = $scheduleFilter->withValue('14:30:00'); // 2:30 PM
 * ```
 * ```
 * // ISO 8601 format processing
 * $isoDateValue = new DatetimeFormatValue('Y-m-d\TH:i:s\Z');
 * $apiFilter = new Gte('timestamp', $isoDateValue);
 * $result = $apiFilter->withValue('2024-01-15T14:30:00Z');
 * ```
 * ```
 * // Month/Year format for reports
 * $monthYearValue = new DatetimeFormatValue('Y-m');
 * $reportFilter = new Equals('report_month', $monthYearValue);
 * $result = $reportFilter->withValue('2024-01'); // January 2024
 * ```
 * ```
 * // Custom readable format
 * $readableValue = new DatetimeFormatValue('F j, Y');
 * $newsFilter = new Gte('publish_date', $readableValue);
 * $result = $newsFilter->withValue('January 15, 2024');
 * ```
 * ```
 * // Format validation examples
 * $strictDateValue = new DatetimeFormatValue('Y-m-d');
 *
 * $strictDateValue->accepts('2024-01-15');    // true - exact format match
 * $strictDateValue->accepts('2024-1-15');     // false - missing leading zero
 * $strictDateValue->accepts('01/15/2024');    // false - different format
 * $strictDateValue->accepts('2024-01-15 10:30'); // false - extra time part
 * $strictDateValue->accepts('invalid-date');   // false - invalid date
 * ```
 * ```
 * // Conversion with output format
 * $convertingValue = new DatetimeFormatValue('Y-m-d', 'F j, Y');
 * $convertingValue->convert('2024-01-15'); // Returns 'January 15, 2024'
 *
 * // Conversion to DateTimeImmutable (no output format)
 * $datetimeValue = new DatetimeFormatValue('Y-m-d');
 * $datetimeValue->convert('2024-01-15'); // Returns DateTimeImmutable object
 * ```
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
