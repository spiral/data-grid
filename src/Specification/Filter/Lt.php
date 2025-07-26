<?php

declare(strict_types=1);

namespace Spiral\DataGrid\Specification\Filter;

/**
 * Filters records where a field value is less than the specified value.
 *
 * Real-world usage examples:
 * - Age filtering: Find users younger than 65 years
 * - Price filtering: Find budget products under $50
 * - Performance monitoring: Find servers with CPU usage < 90%
 * - Deadline filtering: Find tasks due before a specific date
 * - Inventory: Find low-stock products with quantity < 5
 * - Financial: Find small transactions under $100
 * - File size: Find files smaller than 10MB
 * - Score filtering: Find low-performing items with score < 3
 *
 * @example
 * // Find budget-friendly products
 * $priceFilter = new Lt('price', new NumericValue());
 * $result = $priceFilter->withValue(50); // Price < $50
 *
 * @example
 * // Find younger users
 * $ageFilter = new Lt('age', new IntValue());
 * $result = $ageFilter->withValue(30); // Age < 30
 *
 * @example
 * // Low-stock alert
 * $stockFilter = new Lt('quantity', new IntValue());
 * $result = $stockFilter->withValue(5); // Quantity < 5 units
 *
 * @example
 * // System performance alerts
 * $cpuFilter = new Lt('cpu_usage', 90); // CPU usage < 90%
 *
 * @example
 * // File size limitations
 * $sizeFilter = new Lt('file_size_mb', new NumericValue());
 * $result = $sizeFilter->withValue(10); // Files < 10MB
 *
 * @example
 * // Low-rated content
 * $ratingFilter = new Lt('rating', new FloatValue());
 * $result = $ratingFilter->withValue(3.0); // Rating < 3.0 stars
 *
 * @example
 * // Urgent deadlines
 * $deadlineFilter = new Lt('due_date', new DatetimeValue());
 * $result = $deadlineFilter->withValue('2024-12-31'); // Due before 2024-12-31
 */
final class Lt extends Expression {}
