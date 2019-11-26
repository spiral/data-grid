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

final class DatetimeValue implements ValueInterface
{
    /**
     * @inheritDoc
     */
    public function accepts($value): bool
    {
        return is_scalar($value) && $this->convert($value) !== null;
    }

    /**
     * @inheritDoc
     */
    public function convert($value)
    {
        try {
            $value = (string)$value;
            return new \DateTimeImmutable(is_numeric($value) ? "@$value" : $value);
        } catch (\Throwable $e) {
            return null;
        }
    }
}
