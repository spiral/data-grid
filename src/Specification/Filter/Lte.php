<?php

declare(strict_types=1);

namespace Spiral\DataGrid\Specification\Filter;

/**
 * Filters records where a field value is less than or equal to the specified value.
 * 
 * Real-world usage examples:
 * - Maximum limits: Find products priced at $100 or less
 * - Age restrictions: Find users 65 years old or younger
 * - Performance thresholds: Find servers with CPU usage <= 85%
 * - Budget constraints: Find expenses within or under budget limit
 * - Capacity limits: Find events with attendance <= venue capacity
 * - Grade filtering: Find students with scores <= failing threshold
 * - Date boundaries: Find events up to and including specific date
 * - Resource allocation: Find processes using <= available memory
 * 
 * @example
 * // Maximum price filtering
 * $priceFilter = new Lte('price', new NumericValue());
 * $result = $priceFilter->withValue(100); // Price <= $100
 * 
 * @example
 * // Senior discount eligibility
 * $ageFilter = new Lte('age', new IntValue());
 * $result = $ageFilter->withValue(65); // Age <= 65
 * 
 * @example
 * // Performance monitoring
 * $cpuFilter = new Lte('cpu_usage', new FloatValue());
 * $result = $cpuFilter->withValue(85.0); // CPU usage <= 85%
 * 
 * @example
 * // Budget compliance
 * $budgetFilter = new Lte('amount', 1000); // Amount <= $1000
 * 
 * @example
 * // Venue capacity check
 * $capacityFilter = new Lte('attendees', new IntValue());
 * $result = $capacityFilter->withValue(500); // Attendees <= 500
 * 
 * @example
 * // Academic performance
 * $gradeFilter = new Lte('score', new IntValue());
 * $result = $gradeFilter->withValue(59); // Failing scores (score <= 59)
 * 
 * @example
 * // Event date filtering
 * $dateFilter = new Lte('event_date', new DatetimeValue());
 * $result = $dateFilter->withValue('2024-12-31'); // Events on or before 2024-12-31
 * 
 * @example
 * // Memory usage monitoring
 * $memoryFilter = new Lte('memory_usage_gb', new NumericValue());
 * $result = $memoryFilter->withValue(8.0); // Memory usage <= 8GB
 */
final class Lte extends Expression {}
