<?php

/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Valentin Vintsukevich (vvval)
 * @author    Anton Tsitou (Wolfy-J)
 */

declare(strict_types=1);

namespace Spiral\DataGrid;

use Countable;
use Spiral\DataGrid\Exception\CompilerException;
use Spiral\DataGrid\Input\ArrayInput;
use Spiral\DataGrid\Input\NullInput;

/**
 * Generates grid views based on provided inout source and grid specifications.
 */
class GridFactory implements GeneratorInterface
{
    public const KEY_FILTERS     = 'filter';
    public const KEY_SORT        = 'sort';
    public const KEY_PAGINATE    = 'paginate';
    public const KEY_FETCH_COUNT = 'fetchCount';

    /** @var Compiler */
    private $compiler;

    /** @var InputInterface */
    private $input;

    /** @var InputInterface|null */
    private $defaultInput;

    /** @var GridInterface */
    private $view;

    /**
     * @param Compiler            $compiler
     * @param InputInterface|null $input
     * @param GridInterface|null  $view
     */
    public function __construct(Compiler $compiler, InputInterface $input = null, GridInterface $view = null)
    {
        $this->compiler = $compiler;
        $this->input = $input ?? new NullInput();
        $this->defaultInput = new NullInput();
        $this->view = $view ?? new Grid();
    }

    /**
     * Associate new input source with grid generator.
     *
     * @param InputInterface $input
     * @return GridFactory
     */
    public function withInput(InputInterface $input): self
    {
        $generator = clone $this;
        $generator->input = $input;

        return $generator;
    }

    /**
     * USe default filter values.
     *
     * @param array $data
     * @return $this
     */
    public function withDefaults(array $data): self
    {
        $generator = clone $this;
        $generator->defaultInput = new ArrayInput($data);

        return $generator;
    }

    /**
     * Isolate input in a given namespace (won't affect the default input).
     *
     * @param string $namespace
     * @return GridFactory
     */
    public function withNamespace(string $namespace): self
    {
        $generator = clone $this;
        $generator->input = $generator->input->withNamespace($namespace);

        return $generator;
    }

    /**
     * Generate new grid view using given source and data schema.
     *
     * @param mixed      $source
     * @param GridSchema $schema
     * @return GridInterface
     */
    public function create($source, GridSchema $schema): GridInterface
    {
        $view = clone $this->view;

        $filters = [];
        foreach ($this->getOptionArray(static::KEY_FILTERS) ?? [] as $name => $value) {
            if ($schema->hasFilter($name)) {
                $filter = $schema->getFilter($name)->withValue($value);

                if ($filter !== null) {
                    $source = $this->compiler->compile($source, $filter);
                    $filters[$name] = $filter->getValue();
                }
            }
        }
        $view = $view->withOption(GridInterface::FILTERS, $filters);

        if (
            $source instanceof Countable
            && $this->getOption(static::KEY_FETCH_COUNT)
        ) {
            $view = $view->withOption(GridInterface::COUNT, $source->count());
        }

        $sorters = [];
        foreach ($this->getOptionArray(static::KEY_SORT) ?? [] as $name => $value) {
            if ($schema->hasSorter($name)) {
                $sorter = $schema->getSorter($name)->withDirection($value);

                if ($sorter !== null) {
                    $source = $this->compiler->compile($source, $sorter);
                    $sorters[$name] = $sorter->getValue();
                }
            }
        }
        $view = $view->withOption(GridInterface::SORTERS, $sorters);

        if ($schema->getPaginator() !== null) {
            $paginator = $schema->getPaginator()->withValue($this->getOption(static::KEY_PAGINATE));
            if ($paginator === null) {
                throw new CompilerException('The paginator can not be null');
            }

            $source = $this->compiler->compile($source, $paginator);
            $view = $view->withOption(GridInterface::PAGINATOR, $paginator->getValue());
        }

        return $view->withSource($source);
    }

    /**
     * Check if option presented in input.
     *
     * @param string $option
     * @return bool
     */
    private function hasOption(string $option): bool
    {
        return $this->input->hasValue($option) || $this->defaultInput->hasValue($option);
    }

    /**
     * Return array of options for the input. Checks the default input in case of value missing in parent.
     *
     * @param string $option
     * @return mixed
     */
    private function getOption(string $option)
    {
        if ($this->input->hasValue($option)) {
            $result = $this->input->getValue($option);
        } else {
            $result = $this->defaultInput->getValue($option);
        }

        return $result;
    }

    /**
     * Return array of options for the input. Checks the default input in case of value missing in parent.
     *
     * @param string $option
     * @return array
     */
    private function getOptionArray(string $option): array
    {
        $result = $this->getOption($option);
        if (!is_array($result)) {
            return [];
        }

        return $result;
    }
}
