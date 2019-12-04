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
use Spiral\DataGrid\Specification\Value;
use Spiral\DataGrid\SpecificationInterface;

final class PagePaginator implements FilterInterface
{
    /** @var Value\EnumValue */
    private $limitValue;

    /** @var int */
    private $defaultLimit;

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
     * @see \Spiral\DataGrid\Specification\Filter\Map todo can use functonality
     */
    public function withValue($value): ?SpecificationInterface
    {
        $limit = $this->defaultLimit;
        $page = 1;

        if (!is_array($value)) {
            return $this->createSequence($limit, $page);
        }

        if (isset($value['limit']) && $this->limitValue->accepts($value['limit'])) {
            $limit = $this->limitValue->convert($value['limit']);
        }

        if (isset($value['page']) && is_numeric($value['page'])) {
            $page = max((int)$value['page'], 1);
        }

        return $this->createSequence($limit, $page);
    }

    /**
     * No value until paginated.
     */
    public function getValue(): void
    {
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
