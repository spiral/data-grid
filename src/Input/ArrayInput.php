<?php

/**
 * Spiral Framework. PHP Data Grid
 *
 * @license MIT
 * @author  Anton Tsitou (Wolfy-J)
 * @author  Valentin Vintsukevich (vvval)
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
    public function withNamespace(string $namespace): InputInterface
    {
        $input = clone $this;
        $input->data = [];

        $data = $this->getValue($namespace, []);
        if (is_array($data)) {
            $input->data = $data;
        }

        return $input;
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

    /**
     * @inheritDoc
     */
    public function hasValue(string $option): bool
    {
        return array_key_exists($option, $this->data);
    }
}
