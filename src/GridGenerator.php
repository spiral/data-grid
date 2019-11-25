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
use Spiral\DataGrid\Input\NullInput;

/**
 * Generates grid views based on provided inout source and grid specifications.
 */
class GridGenerator implements GeneratorInterface
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

    /** @var GridViewInterface */
    private $view;

    /**
     * @param Compiler               $compiler
     * @param InputInterface|null    $input
     * @param GridViewInterface|null $view
     */
    public function __construct(Compiler $compiler, InputInterface $input = null, GridViewInterface $view = null)
    {
        $this->compiler = $compiler;
        $this->input = $input ?? new NullInput();
        $this->defaultInput = new NullInput();
        $this->view = $view ?? new GridView();
    }

    /**
     * Associate new input source with grid generator.
     *
     * @param InputInterface $input
     * @return GridGenerator
     */
    public function withInput(InputInterface $input): self
    {
        $generator = clone $this;
        $generator->input = $input;

        return $generator;
    }

    /**
     * Isolate input in a given namespace (won't affect the default input).
     *
     * @param string $namespace
     * @return GridGenerator
     */
    public function withNamespace(string $namespace): self
    {
        $generator = clone $this;
        $generator->input = $generator->input->withNamespace($namespace);

        return $generator;
    }

    /**
     * Associate new default input (fallback values) with grid generator.
     *
     * @param InputInterface $input
     * @return GridGenerator
     */
    public function withDefault(InputInterface $input): self
    {
        $generator = clone $this;
        $generator->defaultInput = $input;

        return $generator;
    }

    /**
     * Associate new grid view (presenter) with grid generator.
     *
     * @param GridViewInterface $result
     * @return GridGenerator
     */
    public function withView(GridViewInterface $result): self
    {
        $generator = clone $this;
        $generator->view = $result;

        return $generator;
    }

    /**
     * Generate new grid view using given source and data schema.
     *
     * @param mixed      $source
     * @param GridSchema $schema
     * @return GridViewInterface
     */
    public function generate($source, GridSchema $schema): GridViewInterface
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
        $view = $view->withOption(GridViewInterface::FILTERS, $filters);

        if ($source instanceof Countable && $this->hasOption(static::KEY_FETCH_COUNT)) {
            $view = $view->withOption(GridViewInterface::COUNT, $source->count());
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
        $view = $view->withOption(GridViewInterface::SORTERS, $sorters);

        if ($schema->getPaginator() !== null) {
            $paginator = $schema->getPaginator()->withValue($this->getOption(static::KEY_PAGINATE));
            if ($paginator === null) {
                throw new CompilerException('The paginator can not be null');
            }

            $source = $this->compiler->compile($source, $paginator);
            $view = $view->withOption(GridViewInterface::PAGINATOR, $paginator->getValue());
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
