<?php

/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Valentin Vintsukevich (vvval), Anton Titov (Wolfy-J)
 */

declare(strict_types=1);

namespace Spiral\DataGrid\Specification\Filter;

use Spiral\DataGrid\Specification\FilterInterface;
use Spiral\DataGrid\Specification\ValueInterface;
use Spiral\DataGrid\SpecificationInterface;

abstract class Expression implements FilterInterface
{
    /** @var string */
    protected $expression;

    /** @var ValueInterface|mixed */
    protected $value;

    /**
     * ExpressionFilter constructor.
     *
     * @param string               $expression
     * @param ValueInterface|mixed $value
     */
    public function __construct(string $expression, $value)
    {
        $this->expression = $expression;
        $this->value = $value;
    }

    /**
     * @inheritDoc
     */
    public function withValue($value): ?SpecificationInterface
    {
        $filter = clone $this;

        if (!$filter->value instanceof ValueInterface) {
            // constant value
            return $filter;
        }

        if (!$filter->value->accepts($value)) {
            // invalid value
            return null;
        }

        // create static filtered value
        $filter->value = $filter->value->convert($value);

        return $filter;
    }

    /**
     * @return string
     */
    public function getExpression(): string
    {
        return $this->expression;
    }

    /**
     * @inheritDoc
     */
    public function getValue()
    {
        return $this->value;
    }
}
