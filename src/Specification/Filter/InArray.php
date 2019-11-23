<?php

/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Valentin Vintsukevich (vvval), Anton Titov (Wolfy-J)
 */

declare(strict_types=1);

namespace Spiral\DataGrid\Specification\Filter;

use Spiral\DataGrid\Specification\Value\ArrayValue;
use Spiral\DataGrid\Specification\ValueInterface;

final class InArray extends Expression
{
    /**
     * @param string $expression
     * @param null   $value
     */
    public function __construct(string $expression, $value = null)
    {
        if ($value instanceof ValueInterface && !$value instanceof ArrayValue) {
            $value = new ArrayValue($value);
        }

        parent::__construct($expression, $value);
    }
}
