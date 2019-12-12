<?php

/**
 * Spiral Framework. PHP Data Grid
 *
 * @license MIT
 * @author  Anton Tsitou (Wolfy-J)
 * @author  Valentin Vintsukevich (vvval)
 */

declare(strict_types=1);

namespace Spiral\DataGrid\Specification\Pagination;

use Spiral\DataGrid\Specification\FilterInterface;
use Spiral\DataGrid\Specification\Sequence;
use Spiral\DataGrid\Specification\SequenceInterface;
use Spiral\DataGrid\Specification\Value;
use Spiral\DataGrid\SpecificationInterface;

final class PagePaginator implements SequenceInterface, FilterInterface
{
    /** @var Value\EnumValue */
    private $limitValue;

    /** @var int */
    private $defaultLimit;

    /** @var SequenceInterface|null */
    private $sequence;

    /**
     * @param int   $defaultLimit
     * @param array $allowedLimits
     */
    public function __construct(int $defaultLimit, array $allowedLimits = [])
    {
        $this->defaultLimit = $defaultLimit;

        if (!in_array($defaultLimit, $allowedLimits, true)) {
            $allowedLimits[] = $defaultLimit;
        }

        sort($allowedLimits);

        $this->limitValue = new Value\EnumValue(new Value\IntValue(), ...$allowedLimits);
    }

    /**
     * @param mixed $value
     * @return FilterInterface|null
     */
    public function withValue($value): ?SpecificationInterface
    {
        $paginator = clone $this;

        $limit = $this->defaultLimit;
        $page = 1;

        if (!is_array($value)) {
            $paginator->sequence = $this->createSequence($limit, $page);

            return $paginator;
        }

        if (isset($value['limit']) && $this->limitValue->accepts($value['limit'])) {
            $limit = $this->limitValue->convert($value['limit']);
        }

        if (isset($value['page']) && is_numeric($value['page'])) {
            $page = max((int)$value['page'], 1);
        }

        $paginator->sequence = $this->createSequence($limit, $page);

        return $paginator;
    }

    /**
     * {@inheritDoc}
     */
    public function getSpecifications(): array
    {
        return $this->sequence->getSpecifications();
    }

    /**
     * {@inheritDoc}
     */
    public function getValue()
    {
        return $this->sequence->getValue();
    }

    /**
     * @param int $limit
     * @param int $page
     * @return Sequence
     */
    private function createSequence(int $limit, int $page): Sequence
    {
        $specifications = [new Limit($limit)];
        if ($page > 1) {
            $specifications[] = new Offset($limit * ($page - 1));
        }

        return new Sequence(compact('limit', 'page'), ...$specifications);
    }
}
