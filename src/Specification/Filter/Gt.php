<?php

declare(strict_types=1);

namespace Spiral\DataGrid\Specification\Filter;

/**
 * Filters records where a field value is greater than the specified value.
 *
 * Real-world usage examples:
 * - Age filtering: Find users older than 18 years
 * - Price filtering: Find products more expensive than $100
 * - Performance metrics: Find servers with CPU usage > 80%
 * - Date filtering: Find events after a specific date
 * - Inventory: Find products with stock quantity > 10
 * - Financial: Find transactions above $1000
 * - Analytics: Find pages with views > 1000
 * - Gaming: Find players with score > 500 points
 *
 * @example
 * // Find products more expensive than $50
 * $priceFilter = new Gt('price', new NumericValue());
 * $result = $priceFilter->withValue(50);
 *
 * @example
 * // Find adult users (age > 18)
 * $ageFilter = new Gt('age', new IntValue());
 * $result = $ageFilter->withValue(18);
 *
 * @example
 * // Find recent posts (created after specific date)
 * $dateFilter = new Gt('created_at', new DatetimeValue());
 * $result = $dateFilter->withValue('2024-01-01');
 *
 * @example
 * // Fixed threshold filtering
 * $performanceFilter = new Gt('cpu_usage', 80); // CPU usage > 80%
 *
 * @example
 * // High-value transaction filtering
 * $transactionFilter = new Gt('amount', new NumericValue());
 * $result = $transactionFilter->withValue(1000); // Amount > $1000
 *
 * @example
 * // Popular content filtering
 * $popularFilter = new Gt('view_count', 1000); // Views > 1000
 */
final class Gt extends Expression {}
