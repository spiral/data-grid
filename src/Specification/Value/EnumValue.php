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

final class EnumValue implements ValueInterface
{
    /** @var ValueInterface */
    private $base;

    /** @var array|mixed[] */
    private $values;

    /**
     * @param ValueInterface $base
     * @param mixed          ...$values
     */
    public function __construct(ValueInterface $base, ...$values)
    {
        $this->base = $base;
        $this->values = $values;
    }

    /**
     * @inheritDoc
     */
    public function accepts($value): bool
    {
        if (!$this->base->accepts($value)) {
            return false;
        }

        return in_array($this->base->convert($value), $this->values, true);
    }

    /**
     * @inheritDoc
     */
    public function convert($value)
    {
        return $this->base->convert($value);
    }
}
