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

final class BootValue implements ValueInterface
{
    /**
     * @inheritDoc
     */
    public function accepts($value): bool
    {
        if (is_string($value)) {
            return in_array(strtolower($value), ['0', '1', 'true', 'false'], true);
        }

        return is_bool($value);
    }

    /**
     * @inheritDoc
     */
    public function convert($value)
    {
        if (is_string($value)) {
            switch (strtolower($value)) {
                case '0':
                case 'false':
                    $value = false;
                    break;
                case '1':
                case 'true':
                    $value = true;
            }
        }

        return (bool)$value;
    }
}
