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

final class ArrayValue implements ValueInterface
{
    /** @var ValueInterface */
    private $base;

    /**
     * @param ValueInterface $base
     */
    public function __construct(ValueInterface $base)
    {
        $this->base = $base;
    }

    /**
     * @inheritDoc
     */
    public function accepts($value): bool
    {
        if (!is_array($value)) {
            return false;
        }

        foreach ($value as $child) {
            if (!$this->base->accepts($child)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public function convert($value)
    {
        $result = [];
        foreach ($value as $child) {
            $result[] = $this->base->convert($child);
        }

        return $result;
    }
}
