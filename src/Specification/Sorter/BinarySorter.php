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

final class BinarySorter implements SorterInterface
{
    /** @var SorterInterface */
    private $asc;

    /** @var SorterInterface */
    private $desc;

    /**
     * @param SorterInterface $asc
     * @param SorterInterface $desc
     */
    public function __construct(SorterInterface $asc, SorterInterface $desc)
    {
        $this->asc = $asc;
        $this->desc = $desc;
    }

    /**
     * @inheritDoc
     */
    public function withDirection($direction): ?SpecificationInterface
    {
        if (is_string($direction)) {
            if (strtolower($direction) === 'desc') {
                return $this->desc->withDirection(self::DESC);
            }

            if (strtolower($direction) === 'asc') {
                return $this->asc->withDirection(self::ASC);
            }
        }

        if (in_array($direction, ['-1', -1, SORT_DESC], true)) {
            return $this->desc->withDirection(self::DESC);
        }

        if (in_array($direction, ['1', 1, SORT_ASC], true)) {
            return $this->asc->withDirection(self::ASC);
        }

        return null;
    }

    /**
     * @inheritDoc
     */
    public function getValue(): void
    {
    }
}
