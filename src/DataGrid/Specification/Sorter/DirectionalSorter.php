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

final class DirectionalSorter implements SorterInterface
{
    /** @var SorterInterface */
    private $asc;

    /** @var SorterInterface */
    private $desc;

    /** @var SorterInterface */
    private $sorter;

    /** @var string|null */
    private $direction;

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
        $this->direction = $this->checkDirection($direction);
        switch ($this->direction) {
            case self::ASC:
                $this->sorter = $this->asc->withDirection(self::ASC);
                break;
            case self::DESC:
                $this->sorter = $this->desc->withDirection(self::DESC);
                break;
            default:
                $this->sorter = null;
        }

        return $this->sorter;
    }

    /**
     * @inheritDoc
     */
    public function getValue(): ?string
    {
        return $this->direction;
    }

    /**
     * @param $direction
     * @return string|null
     */
    private function checkDirection($direction): ?string
    {
        if (is_string($direction)) {
            if (strtolower($direction) === self::DESC) {
                return self::DESC;
            }

            if (strtolower($direction) === self::ASC) {
                return self::ASC;
            }
        }

        if (in_array($direction, ['-1', -1, SORT_DESC], true)) {
            return self::DESC;
        }

        if (in_array($direction, ['1', 1, SORT_ASC], true)) {
            return self::ASC;
        }

        return null;
    }
}
