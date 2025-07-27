<?php

declare(strict_types=1);

namespace Spiral\DataGrid\Specification\Filter;

/**
 * Filters records where a field value is greater than or equal to the specified value.
 *
 * ```
 * // Find products at or above minimum price
 * $priceFilter = new Gte('price', new NumericValue());
 * $result = $priceFilter->withValue(50); // Price >= $50
 * ```
 * ```
 * // Age verification (18 or older)
 * $ageFilter = new Gte('age', new IntValue());
 * $result = $ageFilter->withValue(18); // Age >= 18
 * ```
 * ```
 * // Minimum rating filter
 * $ratingFilter = new Gte('rating', new FloatValue());
 * $result = $ratingFilter->withValue(4.0); // Rating >= 4.0 stars
 * ```
 * ```
 * // Stock availability check
 * $stockFilter = new Gte('quantity', 1); // In stock (quantity >= 1)
 * ```
 * ```
 * // Experience level filtering
 * $experienceFilter = new Gte('years_experience', new IntValue());
 * $result = $experienceFilter->withValue(2); // 2+ years experience
 * ```
 * ```
 * // Performance monitoring
 * $performanceFilter = new Gte('uptime_percentage', 99.5); // >= 99.5% uptime
 * ```
 */
final class Gte extends Expression {}
