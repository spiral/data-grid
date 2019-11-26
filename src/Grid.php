<?php

/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

declare(strict_types=1);

namespace Spiral\DataGrid;

use Generator;
use Spiral\DataGrid\Exception\GridViewException;

/**
 * Carries information about compiled selection results and their specifications.
 */
class Grid implements GridInterface
{
    /** @var array */
    private $options = [];

    /** @var iterable */
    private $source;

    /** @var callable */
    private $mapper;

    /**
     * @return Generator
     */
    public function getIterator(): Generator
    {
        if ($this->source === null) {
            throw new GridViewException('GridView does not have associated data source');
        }

        foreach ($this->source as $key => $item) {
            if ($this->mapper === null) {
                yield $key => $item;
                continue;
            }

            yield $key => ($this->mapper)($item);
        }
    }

    /**
     * @inheritDoc
     */
    public function withOption(string $name, $value): GridInterface
    {
        $view = clone $this;
        $view->options[$name] = $value;

        return $view;
    }

    /**
     * @inheritDoc
     */
    public function getOption(string $name)
    {
        return $this->options[$name] ?? null;
    }

    /**
     * @inheritDoc
     */
    public function withSource(iterable $source): GridInterface
    {
        $view = clone $this;
        $view->source = $source;

        return $view;
    }

    /**
     * @inheritDoc
     */
    public function withView(callable $view): GridInterface
    {
        $view = clone $this;
        $view->mapper = $view;

        return $view;
    }
}
