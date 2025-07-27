<?php

declare(strict_types=1);

namespace Spiral\DataGrid\Specification\Filter;

/**
 * Filters records where a field value is greater than the specified value.
 *
 * ```
 * // Find products more expensive than $50
 * $priceFilter = new Gt('price', new NumericValue());
 * $result = $priceFilter->withValue(50);
 * ```
 * ```
 * // Find adult users (age > 18)
 * $ageFilter = new Gt('age', new IntValue());
 * $result = $ageFilter->withValue(18);
 * ```
 * ```
 * // Find recent posts (created after specific date)
 * $dateFilter = new Gt('created_at', new DatetimeValue());
 * $result = $dateFilter->withValue('2024-01-01');
 * ```
 * ```
 * // Fixed threshold filtering
 * $performanceFilter = new Gt('cpu_usage', 80); // CPU usage > 80%
 * ```
 * ```
 * // High-value transaction filtering
 * $transactionFilter = new Gt('amount', new NumericValue());
 * $result = $transactionFilter->withValue(1000); // Amount > $1000
 * ```
 * ```
 * // Popular content filtering
 * $popularFilter = new Gt('view_count', 1000); // Views > 1000
 * ```
 */
final class Gt extends Expression {}
