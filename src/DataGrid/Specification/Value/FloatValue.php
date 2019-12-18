<?php

/**
 * Spiral Framework. PHP Data Grid
 *
 * @license MIT
 * @author  Anton Tsitou (Wolfy-J)
 * @author  Valentin Vintsukevich (vvval)
 */

declare(strict_types=1);

namespace Spiral\DataGrid\Specification\Value;

use Spiral\DataGrid\Specification\ValueInterface;

final class FloatValue implements ValueInterface
{
    /**
     * @inheritDoc
     */
    public function accepts($value): bool
    {
        if (is_string($value)) {
            $value = trim($value);
        }

        return is_numeric($value) || (is_string($value) && $value === '');
    }

    /**
     * @inheritDoc
     * @return float
     */
    public function convert($value): float
    {
        if (is_string($value)) {
            $value = trim($value);
        }

        return (float)$value;
    }
}
