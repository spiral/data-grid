<?php

declare(strict_types=1);

namespace Spiral\DataGrid\Specification\Filter;

use Spiral\DataGrid\Specification\FilterInterface;
use Spiral\DataGrid\SpecificationInterface;

/**
 * Abstract base class for filter groups that combine multiple filters.
 *
 * Provides common functionality for filters that contain and operate on
 * collections of other filters, such as AND/OR logic combinations.
 *
 * ```
 * // Custom group filter
 * class CustomGroup extends Group {
 *     public function withValue(mixed $value): ?SpecificationInterface {
 *         // Custom logic for combining filters
 *         return $this->combineFilters($value);
 *     }
 * }
 * ```
 *
 * ```
 * // Accessing grouped filters
 * $group = new All($filter1, $filter2, $filter3);
 * $filters = $group->getFilters(); // Get all contained filters
 * ```
 *
 * @see All For AND logic combination
 * @see Any For OR logic combination
 * @see Map For named filter mapping
 * @see Select For predefined filter selection
 */
abstract class Group implements FilterInterface
{
    /** @var FilterInterface[] */
    protected array $filters = [];

    private mixed $value = null;

    abstract public function withValue(mixed $value): ?SpecificationInterface;

    /**
     * Get all filters contained in this group.
     *
     * @return FilterInterface[]
     */
    public function getFilters(): array
    {
        return $this->filters;
    }

    public function getValue(): mixed
    {
        return $this->value;
    }

    /**
     * Create a clone of this group with a new value, resetting the filters array.
     *
     * @param mixed $value The new value to associate with the cloned group
     * @return static A new instance with empty filters array and the specified value
     */
    protected function clone(mixed $value): self
    {
        $group = clone $this;
        $group->filters = [];
        $group->value = $value;

        return $group;
    }
}
