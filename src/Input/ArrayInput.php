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

final class ArrayInput implements InputInterface
{
    /** @var array */
    private $data;

    /**
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * @inheritDoc
     */
    public function withNamespace(string $prefix): InputInterface
    {
        $input = clone $this;
        $input->data = [];

        $ns = $this->getValue($prefix, []);
        if (is_array($ns)) {
            $input->data = $ns;
        }

        return $input;
    }

    /**
     * @inheritDoc
     */
    public function hasValue(string $option): bool
    {
        return array_key_exists($option, $this->data);
    }

    /**
     * @inheritDoc
     */
    public function getValue(string $option, $default = null)
    {
        if (!$this->hasValue($option)) {
            return $default;
        }

        return $this->data[$option];
    }
}
