<?php

/**
 * Spiral Framework. PHP Data Grid
 *
 * @author Valentin Vintsukevich (vvval)
 */

declare(strict_types=1);

namespace Spiral\DataGrid\Specification\Sorter;

use Spiral\DataGrid\Specification\SequentialSpecification;
use Spiral\DataGrid\Specification\SorterInterface;
use Spiral\DataGrid\SpecificationInterface;

class SequentialSorter extends SequentialSpecification implements SorterInterface
{
    /**
     * @inheritdoc
     */
    public function withDirection($direction): ?SpecificationInterface
    {
        return $this;
    }
}
