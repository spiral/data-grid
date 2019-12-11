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
use Spiral\DataGrid\Specification\Value\NumericValue;
use Spiral\Tests\DataGrid\Fixture\WriterOne;

class GridFactoryTest extends TestCase
{
    public function testEmpty(): void
    {
//        $factory = $factory->withNamespace('');

        $factory = $this->factory();
        $grid = $factory->create([], new GridSchema());

        $this->assertNull($grid->getOption('option'));
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
     * @dataProvider inputProvider
     * @param array           $defaults
     * @param string          $name
     * @param FilterInterface $filter
     * @param array           $expected
     */
    public function testDefaults(array $defaults, string $name, FilterInterface $filter, array $expected): void
    {
        $factory = $this->factory();
        $factory = $factory->withDefaults([GridFactory::KEY_FILTERS => $defaults]);

        $this->runInputAssertions($factory, $name, $filter, $expected);
    }

    /**
     * @dataProvider inputProvider
     * @param array           $input
     * @param string          $name
     * @param FilterInterface $filter
     * @param array           $expected
     */
    public function testInput(array $input, string $name, FilterInterface $filter, array $expected): void
    {
        $factory = $this->factory();
        $factory = $factory->withInput(new ArrayInput([GridFactory::KEY_FILTERS => $input]));

        $this->runInputAssertions($factory, $name, $filter, $expected);
    }

    /**
     * @return iterable
     */
    public function inputProvider(): iterable
    {
        return [
            //Value convert applied
            [['filter' => '123'], 'filter', new Equals('field', new NumericValue()), ['filter' => 123]],
            //not applicable value
            [['filter' => 'filter value'], 'filter', new Equals('field', new NumericValue()), []],
            //defined filter value applied
            [['filter' => 'filter value'], 'filter', new Equals('field', 'value'), ['filter' => 'value']],
            //unknown filter
            [['filter' => 'filter value'], 'another filter', new Equals('field', 'value'), []],
        ];
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
     * @param GridFactory     $factory
     * @param string          $name
     * @param FilterInterface $filter
     * @param array           $expected
     */
    private function runInputAssertions(
        GridFactory $factory,
        string $name,
        FilterInterface $filter,
        array $expected
    ): void {
        $schema = new GridSchema();
        $schema->addFilter($name, $filter);
        $grid = $factory->create([], $schema);

        $this->assertEquals($expected, $grid->getOption(GridInterface::FILTERS));
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
