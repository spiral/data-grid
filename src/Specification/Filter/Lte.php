<?php

declare(strict_types=1);

namespace Spiral\DataGrid\Specification\Filter;

/**
 * Filters records where a field value is less than or equal to the specified value.
 *
 * ```
 * // Maximum price filtering
 * $priceFilter = new Lte('price', new NumericValue());
 * $result = $priceFilter->withValue(100); // Price <= $100
 * ```
 * ```
 * // Senior discount eligibility
 * $ageFilter = new Lte('age', new IntValue());
 * $result = $ageFilter->withValue(65); // Age <= 65
 * ```
 * ```
 * // Performance monitoring
 * $cpuFilter = new Lte('cpu_usage', new FloatValue());
 * $result = $cpuFilter->withValue(85.0); // CPU usage <= 85%
 * ```
 * ```
 * // Budget compliance
 * $budgetFilter = new Lte('amount', 1000); // Amount <= $1000
 * ```
 * ```
 * // Venue capacity check
 * $capacityFilter = new Lte('attendees', new IntValue());
 * $result = $capacityFilter->withValue(500); // Attendees <= 500
 * ```
 * ```
 * // Academic performance
 * $gradeFilter = new Lte('score', new IntValue());
 * $result = $gradeFilter->withValue(59); // Failing scores (score <= 59)
 * ```
 * ```
 * // Event date filtering
 * $dateFilter = new Lte('event_date', new DatetimeValue());
 * $result = $dateFilter->withValue('2024-12-31'); // Events on or before 2024-12-31
 * ```
 * ```
 * // Memory usage monitoring
 * $memoryFilter = new Lte('memory_usage_gb', new NumericValue());
 * $result = $memoryFilter->withValue(8.0); // Memory usage <= 8GB
 * ```
 */
final class Lte extends Expression {}
