<?php

/**
 * Spiral Framework. PHP Data Grid
 *
 * @author Valentin Vintsukevich (vvval)
 */

declare(strict_types=1);

namespace Spiral\DataGrid\Specification\Filter;

use Spiral\DataGrid\Specification\FilterInterface;
use Spiral\DataGrid\Specification\SequentialSpecification;
use Spiral\DataGrid\SpecificationInterface;

class SequentialFilter extends SequentialSpecification implements FilterInterface
{
    /**
     * @inheritdoc
     */
    public function withValue($value): ?SpecificationInterface
    {
        return $this;
    }
}
