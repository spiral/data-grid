<?php

/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

declare(strict_types=1);

namespace Spiral\DataGrid\Input;

use Spiral\DataGrid\InputInterface;

final class NullInput implements InputInterface
{
    /**
     * @inheritDoc
     */
    public function withNamespace(string $prefix): InputInterface
    {
        return clone $this;
    }

    /**
     * @inheritDoc
     */
    public function hasValue(string $option): bool
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function getValue(string $option, $default = null)
    {
        return $default;
    }
}
