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

final class All implements FilterInterface
{
    /** @var FilterInterface[] */
    private $filters;

    /**
     * @param FilterInterface ...$filter
     */
    public function __construct(FilterInterface ...$filter)
    {
        $this->filters = $filter;
    }

    /**
     * @inheritDoc
     */
    public function withValue($value): ?SpecificationInterface
    {
        $filter = clone $this;
        $filter->filters = [];

        foreach ($this->filters as $f) {
            $applied = $f->withValue($value);

            if ($applied === null) {
                // all nested filters must be configured
                return null;
            }

            $filter->filters[] = $applied;
        }

        return $filter;
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
     * @return mixed|null
     */
    public function getValue()
    {
        if (count($this->filters) > 0) {
            return array_values($this->filters)[0]->getValue();
        }

        return null;
    }
}
