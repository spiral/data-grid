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

use Spiral\DataGrid\Exception\ValueException;
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
        $this->values = $this->convertEnum($values);
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

    /**
     * @param array $values
     * @return array
     */
    private function convertEnum(array $values): array
    {
        if (empty($values)) {
            throw new ValueException('Enum set should not be empty');
        }

        $type = new ArrayValue($this->base);
        if (!$type->accepts($values)) {
            throw new ValueException(sprintf(
                '"Got non-compatible values, expected only compatible with `%s`.',
                get_class($this->base)
            ));
        }

        return $type->convert($values);
    }
}
