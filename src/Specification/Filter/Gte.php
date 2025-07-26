<?php

declare(strict_types=1);

namespace Spiral\DataGrid\Specification\Filter;

/**
 * Filters records where a field value is greater than or equal to the specified value.
 *
 * Real-world usage examples:
 * - Age requirements: Find users who are 18 years or older
 * - Minimum price: Find products priced at $50 or more
 * - Performance thresholds: Find servers with CPU usage >= 70%
 * - Date ranges: Find events from today onwards
 * - Stock levels: Find products with quantity >= 5 units
 * - Rating filters: Find reviews with 4+ stars
 * - Salary ranges: Find jobs paying >= $60,000
 * - Experience levels: Find candidates with >= 2 years experience
 *
 * @example
 * // Find products at or above minimum price
 * $priceFilter = new Gte('price', new NumericValue());
 * $result = $priceFilter->withValue(50); // Price >= $50
 *
 * @example
 * // Age verification (18 or older)
 * $ageFilter = new Gte('age', new IntValue());
 * $result = $ageFilter->withValue(18); // Age >= 18
 *
 * @example
 * // Minimum rating filter
 * $ratingFilter = new Gte('rating', new FloatValue());
 * $result = $ratingFilter->withValue(4.0); // Rating >= 4.0 stars
 *
 * @example
 * // Stock availability check
 * $stockFilter = new Gte('quantity', 1); // In stock (quantity >= 1)
 *
 * @example
 * // Experience level filtering
 * $experienceFilter = new Gte('years_experience', new IntValue());
 * $result = $experienceFilter->withValue(2); // 2+ years experience
 *
 * @example
 * // Performance monitoring
 * $performanceFilter = new Gte('uptime_percentage', 99.5); // >= 99.5% uptime
 */
final class Gte extends Expression {}
