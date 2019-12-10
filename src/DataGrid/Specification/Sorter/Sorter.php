<?php

/**
 * Spiral Framework. PHP Data Grid
 *
 * @license MIT
 * @author  Anton Tsitou (Wolfy-J)
 * @author  Valentin Vintsukevich (vvval)
 */

declare(strict_types=1);

namespace Spiral\DataGrid\Specification\Sorter;

use Spiral\DataGrid\Specification\SorterInterface;
use Spiral\DataGrid\SpecificationInterface;

final class Sorter implements SorterInterface
{
    /** @var DirectionalSorter */
    private $sorter;

    /**
     * @param string ...$expressions
     */
    public function __construct(string ...$expressions)
    {
        $this->sorter = new DirectionalSorter(new AscSorter(...$expressions), new DescSorter(...$expressions));
    }

    /**
     * @inheritDoc
     */
    public function withDirection($direction): ?SpecificationInterface
    {
        return $this->sorter->withDirection($direction);
    }

    /**
     * @inheritDoc
     */
    public function getValue()
    {
        return $this->sorter->getValue();
    }
}
