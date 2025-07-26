<?php

declare(strict_types=1);

namespace Spiral\DataGrid\Specification\Filter;

use Spiral\DataGrid\Specification\FilterInterface;
use Spiral\DataGrid\SpecificationInterface;

use function Spiral\DataGrid\hasKey;

/**
 * Complex filter that maps input array values to multiple named sub-filters.
 * Each sub-filter is applied using its corresponding value from the input array.
 * All mapped values must be provided for the filter to be valid.
 *
 * Real-world usage examples:
 * - Range filters: Map 'min' and 'max' values to separate greater-than and less-than filters
 * - Date ranges: Map 'start_date' and 'end_date' to separate date boundary filters
 * - Search criteria: Map 'title', 'description', and 'tags' to separate LIKE filters
 * - Price ranges: Map 'min_price' and 'max_price' with different comparison operators
 * - Geographic bounds: Map 'north', 'south', 'east', 'west' to coordinate boundary filters
 * - Multi-field forms: Handle complex form inputs with multiple related filter criteria
 * - Analytics filters: Map different metric ranges like 'min_views', 'max_clicks', etc.
 *
 * @example
 * // Price range filter with min/max mapping
 * $priceRangeFilter = new Map([
 *     'min' => new Gte('price', new NumericValue()),
 *     'max' => new Lte('price', new NumericValue())
 * ]);
 * $result = $priceRangeFilter->withValue(['min' => 50, 'max' => 200]);
 * // Applies: price >= 50 AND price <= 200
 *
 * @example
 * // Date range filtering
 * $dateRangeFilter = new Map([
 *     'from' => new Gte('created_at', new DatetimeValue()),
 *     'to' => new Lte('created_at', new DatetimeValue())
 * ]);
 * $result = $dateRangeFilter->withValue([
 *     'from' => '2024-01-01',
 *     'to' => '2024-12-31'
 * ]);
 *
 * @example
 * // Multi-field search
 * $searchFilter = new Map([
 *     'title' => new Like('title', new StringValue()),
 *     'description' => new Like('description', new StringValue()),
 *     'author' => new Equals('author_id', new IntValue())
 * ]);
 * $result = $searchFilter->withValue([
 *     'title' => 'PHP Tutorial',
 *     'description' => 'beginner',
 *     'author' => 123
 * ]);
 *
 * @example
 * // Geographic bounding box
 * $geoFilter = new Map([
 *     'north' => new Lte('latitude', new FloatValue()),
 *     'south' => new Gte('latitude', new FloatValue()),
 *     'east' => new Lte('longitude', new FloatValue()),
 *     'west' => new Gte('longitude', new FloatValue())
 * ]);
 * $result = $geoFilter->withValue([
 *     'north' => 40.7829,
 *     'south' => 40.7489,
 *     'east' => -73.9441,
 *     'west' => -73.9927
 * ]);
 *
 * @example
 * // User profile filtering
 * $profileFilter = new Map([
 *     'min_age' => new Gte('age', new IntValue()),
 *     'location' => new Equals('city', new StringValue()),
 *     'interests' => new InArray('interests', new StringValue())
 * ]);
 * $result = $profileFilter->withValue([
 *     'min_age' => 25,
 *     'location' => 'New York',
 *     'interests' => ['technology', 'music']
 * ]);
 */
final class Map extends Group
{
    /**
     * @param array|FilterInterface[] $filters Associative array of filter names to FilterInterface instances
     */
    public function __construct(array $filters)
    {
        $this->filters = $filters;
    }

    public function withValue(mixed $value): ?SpecificationInterface
    {
        if (!\is_array($value)) {
            // only array values are expected
            return null;
        }

        $map = $this->clone($value);
        foreach ($this->filters as $name => $filter) {
            $name = (string) $name;
            if (!hasKey($value, $name)) {
                // all values must be provided
                return null;
            }

            $applied = $filter->withValue($value[$name]);
            if ($applied === null) {
                return null;
            }

            $map->filters[$name] = $applied;
        }

        return !empty($map->filters) ? $map : null;
    }
}
