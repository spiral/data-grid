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
     */
    public function getValue()
    {
        foreach ($this->filters as $filter) {
            return $filter->getValue();
        }

        return null;
    }
}
