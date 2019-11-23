<?php

/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Valentin Vintsukevich (vvval)
 * @author    Anton Tsitou (Wolfy-J)
 */

declare(strict_types=1);

namespace Spiral\DataGrid\Specification\Filter;

use Spiral\DataGrid\Specification\FilterInterface;
use Spiral\DataGrid\SpecificationInterface;

/**
 * Complex filter provides the ability to distribute complex array value acorss multiple
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
            if (!array_key_exists($name, $value)) {
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
     * @return array|FilterInterface[]
     */
    public function getFilters(): array
    {
        return $this->filters;
    }

    /**
     * @inheritDoc
     */
    public function getValue()
    {
        $result = [];
        foreach ($this->filters as $name => $filter) {
            $result[$name] = $filter;
        }

        return $result;
    }
}
