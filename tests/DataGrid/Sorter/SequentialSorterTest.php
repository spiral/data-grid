<?php

/**
 * Spiral Framework. PHP Data Grid
 *
 * @author Valentin Vintsukevich (vvval)
 */

declare(strict_types=1);

namespace Spiral\Tests\DataGrid\Sorter;

use Exception;
use PHPUnit\Framework\TestCase;
use Spiral\DataGrid\Compiler;
use Spiral\DataGrid\GridFactory;
use Spiral\DataGrid\GridSchema;
use Spiral\DataGrid\Input\ArrayInput;
use Spiral\DataGrid\Specification\Filter;
use Spiral\DataGrid\Specification\Sorter;
use Spiral\Tests\DataGrid\Fixture\SequenceWriter;

class SequentialSorterTest extends TestCase
{
    private const NAME = 'sequence';

    /**
     * @throws Exception
     */
    public function testSequence(): void
    {
        $factory = new GridFactory($this->makeCompiler(), new ArrayInput([
            GridFactory::KEY_SORT => [self::NAME => 1]
        ]));
        $grid = $factory->create([], $this->makeSchema());
        $source = iterator_to_array($grid->getIterator());

        $this->assertCount(2, $source);
        $this->assertEquals(Filter\Equals::class, $source[0]);
        $this->assertEquals(Sorter\AscSorter::class, $source[1]);
    }

    private function makeCompiler(): Compiler
    {
        $compiler = new Compiler();
        $compiler->addWriter(new SequenceWriter());

        return $compiler;
    }

    private function makeSchema(): GridSchema
    {
        $schema = new GridSchema();
        $schema->addSorter(self::NAME, $this->makeSorter());

        return $schema;
    }

    private function makeSorter(): Sorter\SequentialSorter
    {
        return new Sorter\SequentialSorter(
            new Filter\Equals('field', 1),
            new Sorter\AscSorter('field')
        );
    }
}
