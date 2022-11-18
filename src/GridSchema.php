<?php

declare(strict_types=1);

namespace Spiral\DataGrid;

use Spiral\DataGrid\Exception\SchemaException;
use Spiral\DataGrid\Specification\FilterInterface;
use Spiral\DataGrid\Specification\SorterInterface;

/**
 * DataSchema describe the set of available filters, sorting and pagination mode for the underlying data source. Class
 * operates as isolated configuration source.
 */
class GridSchema
{
    /** @var array<non-empty-lowercase-string, FilterInterface> */
    protected array $filters = [];

    /** @var array<non-empty-lowercase-string, SorterInterface> */
    protected array $sorters = [];

    protected ?FilterInterface $paginator = null;

    /**
     * Define new data filter.
     *
     * @throws SchemaException
     */
    public function addFilter(string $name, FilterInterface $filter): void
    {
        if ($this->hasFilter($name)) {
            throw new SchemaException(\sprintf('Filter `%s` is already defined', $name));
        }

        $this->filters[\strtolower($name)] = $filter;
    }

    public function hasFilter(string $name): bool
    {
        return isset($this->filters[\strtolower($name)]);
    }

    /**
     * Get the filter configuration.
     *
     * @throws SchemaException
     */
    public function getFilter(string $name): FilterInterface
    {
        if (!$this->hasFilter($name)) {
            throw new SchemaException(\sprintf('No such filter `%s`', $name));
        }

        return $this->filters[\strtolower($name)];
    }

    /**
     * @return FilterInterface[]
     */
    public function getFilters(): array
    {
        return $this->filters;
    }

    /**
     * Define new value sorter.
     *
     * @throws SchemaException
     */
    public function addSorter(string $name, SorterInterface $sorter): void
    {
        if ($this->hasSorter($name)) {
            throw new SchemaException(\sprintf('Sorter `%s` is already defined', $name));
        }

        $this->sorters[\strtolower($name)] = $sorter;
    }

    public function hasSorter(string $name): bool
    {
        return isset($this->sorters[\strtolower($name)]);
    }

    /**
     * Get the sorter configuration.
     *
     * @throws SchemaException
     */
    public function getSorter(string $name): SorterInterface
    {
        if (!$this->hasSorter($name)) {
            throw new SchemaException(\sprintf('No such sorter `%s`', $name));
        }

        return $this->sorters[\strtolower($name)];
    }

    /**
     * @return SorterInterface[]
     */
    public function getSorters(): array
    {
        return $this->sorters;
    }

    /**
     * Set the pagination filter.
     */
    public function setPaginator(FilterInterface $paginator): void
    {
        $this->paginator = $paginator;
    }

    /**
     * Get the pagination configuration associated with data source. When null - no pagination can be applied.
     */
    public function getPaginator(): ?FilterInterface
    {
        return $this->paginator;
    }
}
