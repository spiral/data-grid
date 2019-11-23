<?php

/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

declare(strict_types=1);

namespace Spiral\DataGrid;

interface InputInterface
{
    /**
     * Isolate the input into given namespace (prefix).
     *
     * @param string $prefix
     * @return InputInterface
     */
    public function withNamespace(string $prefix): InputInterface;

    /**
     * @param string $option
     * @return bool
     */
    public function hasValue(string $option): bool;

    /**
     * @param string $option
     * @param null   $default
     * @return mixed
     */
    public function getValue(string $option, $default = null);
}
