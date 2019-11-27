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

        $this->limitValue = new Value\EnumValue(new Value\IntValue(), ...$allowedLimits);
    }

    /**
     * @param mixed $value
     * @return FilterInterface|null
     */
    public function withValue($value): ?SpecificationInterface
    {
        $sequence = [
            'limit' => $this->defaultLimit,
            'page'  => 1
        ];

        if (!is_array($value)) {
            return $this->createSequence($sequence);
        }

        if (isset($value['limit']) && $this->limitValue->accepts($value['limit'])) {
            $sequence['limit'] = $this->limitValue->convert($value['limit']);
        }

        if (isset($value['page']) && is_numeric($value['page'])) {
            $sequence['page'] = max((int)$value['page'], 1);
        }

        return $this->createSequence($sequence);
    }

    /**
     * No value until paginated.
     */
    public function getValue(): void
    {
    }

    /**
     * @param array $sequence
     * @return Sequence
     */
    private function createSequence(array $sequence): Sequence
    {
        if ($sequence['page'] > 1) {
            return new Sequence(
                $sequence,
                new Limit($sequence['limit']),
                new Offset($sequence['limit'] * ($sequence['page'] - 1))
            );
        }

        return new Sequence($sequence, new Limit($sequence['limit']));
    }
}
