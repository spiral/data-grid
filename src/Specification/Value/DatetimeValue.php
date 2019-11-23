<?php

/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
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
