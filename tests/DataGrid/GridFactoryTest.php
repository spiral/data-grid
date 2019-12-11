<?php

/**
 * Spiral Framework. PHP Data Grid
 *
 * @author Valentin Vintsukevich (vvval)
 */

declare(strict_types=1);

namespace Spiral\Tests\DataGrid;

use PHPUnit\Framework\TestCase;
use Spiral\DataGrid\Compiler;
use Spiral\DataGrid\GridFactory;
use Spiral\DataGrid\GridInterface;
use Spiral\DataGrid\GridSchema;
use Spiral\DataGrid\Input\ArrayInput;
use Spiral\DataGrid\Specification\Filter\Equals;
use Spiral\DataGrid\Specification\FilterInterface;
use Spiral\DataGrid\Specification\Sorter\Sorter;
use Spiral\DataGrid\Specification\SorterInterface;
use Spiral\DataGrid\Specification\Value;
use Spiral\Tests\DataGrid\Fixture\WriterOne;

class GridFactoryTest extends TestCase
{
    public function testEmpty(): void
    {
        $factory = $this->factory();
        $grid = $factory->create([], new GridSchema());

        $this->assertNull($grid->getOption('option'));
    }

    public function testNamespace(): void
    {
        //todo
//        $factory = $factory->withNamespace('');

        $this->assertTrue(true);
    }

    /**
     * @dataProvider filtersProvider
     * @param array           $input
     * @param array           $defaults
     * @param string          $name
     * @param FilterInterface $filter
     * @param array           $expected
     */
    public function testFilters(
        array $input,
        array $defaults,
        string $name,
        FilterInterface $filter,
        array $expected
    ): void {
        $factory = $this->factory();
        $factory = $factory->withDefaults($defaults);
        $factory = $factory->withInput(new ArrayInput($input));

        $schema = new GridSchema();
        $schema->addFilter($name, $filter);
        $grid = $factory->create([], $schema);

        $this->assertEquals($expected, $grid->getOption(GridInterface::FILTERS));
    }

    /**
     * @return iterable
     */
    public function filtersProvider(): iterable
    {
        $stringFilter = new Equals('field', new Value\StringValue());
        $numericFilter = new Equals('field', new Value\NumericValue());

        return [
            //filters are not array
            [[GridFactory::KEY_FILTER => 'scalar value'], [], 'filter', $stringFilter, []],
            [[], [GridFactory::KEY_FILTER => 'scalar value'], 'filter', $stringFilter, []],

            //filters do not match schema
            [[GridFactory::KEY_FILTER => ['filter' => 'value']], [], 'filter 2', $stringFilter, []],
            [[], [GridFactory::KEY_FILTER => ['filter' => 'value']], 'filter 2', $stringFilter, []],

            //filters do not match expected value type
            [[GridFactory::KEY_FILTER => ['filter' => 'value']], [], 'filter', $numericFilter, []],
            [[], [GridFactory::KEY_FILTER => ['filter' => 'value']], 'filter', $numericFilter, []],

            //filters match schema
            [[GridFactory::KEY_FILTER => ['filter' => 'value']], [], 'filter', $stringFilter, ['filter' => 'value']],
            [[], [GridFactory::KEY_FILTER => ['filter' => 'value']], 'filter', $stringFilter, ['filter' => 'value']],
            [[GridFactory::KEY_FILTER => ['filter' => '123']], [], 'filter', $numericFilter, ['filter' => 123]],
            [[], [GridFactory::KEY_FILTER => ['filter' => 123]], 'filter', $numericFilter, ['filter' => 123]],
        ];
    }

    public function testFetchCount(): void
    {
        $factory = $this->factory();

        $this->runFetchCountAssertions($factory, [], null);

        $factory = $factory->withDefaults([GridFactory::KEY_FETCH_COUNT => false]); //whatever value
        $this->runFetchCountAssertions($factory, [], 0);

        $factory = $factory->withInput(new ArrayInput([GridFactory::KEY_FETCH_COUNT => false])); //whatever value
        $this->runFetchCountAssertions($factory, [''], 1);
    }

    /**
     * @dataProvider sortersProvider
     * @param array           $input
     * @param array           $defaults
     * @param string          $name
     * @param SorterInterface $sorter
     * @param array           $expected
     */
    public function testSorters(
        array $input,
        array $defaults,
        string $name,
        SorterInterface $sorter,
        array $expected
    ): void {
        $factory = $this->factory();
        $factory = $factory->withDefaults($defaults);
        $factory = $factory->withInput(new ArrayInput($input));

        $schema = new GridSchema();
        $schema->addSorter($name, $sorter);
        $grid = $factory->create([], $schema);

        $this->assertEquals($expected, $grid->getOption(GridInterface::SORTERS));
    }

    /**
     * @return iterable
     */
    public function sortersProvider(): iterable
    {
        $sorter = new Sorter('id');

        return [
            //sorters are not array
            [[GridFactory::KEY_SORT => 'asc'], [], 'sorter', $sorter, []],
            [[], [GridFactory::KEY_SORT => 'desc'], 'sorter', $sorter, []],

            //sorters do not match schema
            [[GridFactory::KEY_SORT => ['sorter' => 'asc']], [], 'sorter 2', $sorter, []],
            [[], [GridFactory::KEY_SORT => ['sorter' => 'desc']], 'sorter 2', $sorter, []],

            //sorters do not match expected value
            [[GridFactory::KEY_SORT => ['sorter' => 'ascending']], [], 'sorter', $sorter, []],
            [[], [GridFactory::KEY_SORT => ['sorter' => 'descending']], 'sorter', $sorter, []],

            //sorters match schema
            [[GridFactory::KEY_SORT => ['sorter' => 'asc']], [], 'sorter', $sorter, ['sorter' => 'asc']],
            [[GridFactory::KEY_SORT => ['sorter' => 'ASC']], [], 'sorter', $sorter, ['sorter' => 'asc']],
            [[GridFactory::KEY_SORT => ['sorter' => 1]], [], 'sorter', $sorter, ['sorter' => 'asc']],
            [[GridFactory::KEY_SORT => ['sorter' => '1']], [], 'sorter', $sorter, ['sorter' => 'asc']],
            [[], [GridFactory::KEY_SORT => ['sorter' => 'desc']], 'sorter', $sorter, ['sorter' => 'desc']],
            [[], [GridFactory::KEY_SORT => ['sorter' => 'DESC']], 'sorter', $sorter, ['sorter' => 'desc']],
            [[], [GridFactory::KEY_SORT => ['sorter' => -1]], 'sorter', $sorter, ['sorter' => 'desc']],
            [[], [GridFactory::KEY_SORT => ['sorter' => '-1']], 'sorter', $sorter, ['sorter' => 'desc']],
        ];
    }

    public function testPaginator(): void
    {
        //todo
        $this->assertTrue(true);
    }

    /**
     * @return GridFactory
     */
    private function factory(): GridFactory
    {
        $compiler = new Compiler();
        $compiler->addWriter(new WriterOne());

        return new GridFactory($compiler);
    }

    /**
     * @param GridFactory $factory
     * @param             $source
     * @param             $expected
     */
    private function runFetchCountAssertions(GridFactory $factory, $source, $expected): void
    {
        $grid = $factory->create($source, new GridSchema());
        $this->assertEquals($expected, $grid->getOption(GridInterface::COUNT));
    }
}
