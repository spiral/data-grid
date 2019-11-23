<?php

/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Valentin Vintsukevich (vvval), Anton Titov (Wolfy-J)
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
