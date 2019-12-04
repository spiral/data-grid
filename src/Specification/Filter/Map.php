<?php

/**
 * Spiral Framework. PHP Data Grid
 *
 * @license MIT
 * @author  Anton Tsitou (Wolfy-J)
 * @author  Valentin Vintsukevich (vvval)
 */

declare(strict_types=1);

namespace Spiral\DataGrid\Specification\Filter;

use Spiral\DataGrid\Specification\FilterInterface;
use Spiral\DataGrid\SpecificationInterface;

/**
 * Complex filter provides the ability to distribute complex array value across multiple
 * nested filters.
 */
final class Map implements FilterInterface
{
    /** @var FilterInterface[] */
    private $filters;

    /**
     * @param array $filters
     */
    public function __construct(array $filters)
    {
        $this->filters = $filters;
    }

    /**
     * @inheritDoc
     */
    public function withValue($value): ?SpecificationInterface
    {
        if (!is_array($value)) {
            // only array values are expected
            return null;
        }

        $map = clone $this;
        $map->filters = [];

        foreach ($this->filters as $name => $filter) {
            if (!$this->hasKey($value, (string)$name)) {
                // all values must be provided
                return null;
            }

            $applied = $filter->withValue($value[$name]);
            if ($applied === null) {
                return null;
            }

            $map->filters[$name] = $applied;
        }

        return $map;
    }

    /**
     * @return FilterInterface[]
     */
    public function getFilters(): array
    {
        return $this->filters;
    }

    /**
     * @inheritDoc
     */
    public function getValue(): array
    {
        foreach ($this->filters as $filter) {
            return $filter->getValue();
        }

        return null;
    }

    /**
     * @param array  $array
     * @param string $search
     * @return bool
     */
    private function hasKey(array $array, string $search): bool
    {
        foreach ($array as $key => $value) {
            if (strcasecmp($key, $search) === 0) {
                return true;
            }
        }

        return false;
    }
}
