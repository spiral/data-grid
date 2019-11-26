<?php

/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

declare(strict_types=1);

namespace Spiral\Tests\DataGrid;

use PHPUnit\Framework\TestCase;
use Spiral\Database\Database;
use Spiral\Database\Driver\SQLite\SQLiteDriver;
use Spiral\Database\Query\Interpolator;
use Spiral\Database\Query\SelectQuery;
use Spiral\DataGrid\Compiler;
use Spiral\DataGrid\GridHydrator;
use Spiral\DataGrid\Specification\Filter;
use Spiral\DataGrid\Specification\Pagination;
use Spiral\DataGrid\Specification\Sorter;
use Spiral\DataGrid\Writer\QueryWriter;

class QueryWriterTest extends TestCase
{
    public function testLimit(): void
    {
        $select = $this->initCompiler()->compile(
            $this->initQuery(),
            new Pagination\Limit(10)
        );

        $this->assertEqualSQL(
            'SELECT * FROM "users" LIMIT 10',
            $select
        );
    }

    public function testLimitOffset(): void
    {
        $select = $this->initCompiler()->compile(
            $this->initQuery(),
            new Pagination\Limit(10),
            new Pagination\Offset(100)
        );

        $this->assertEqualSQL(
            'SELECT * FROM "users" LIMIT 10 OFFSET 100',
            $select
        );
    }

    public function testEquals(): void
    {
        $select = $this->initCompiler()->compile(
            $this->initQuery(),
            new Filter\Equals('name', 'Antony')
        );

        $this->assertEqualSQL(
            'SELECT * FROM "users" WHERE "name" = \'Antony\'',
            $select
        );
    }

    public function testLike(): void
    {
        $select = $this->initCompiler()->compile(
            $this->initQuery(),
            new Filter\Like('name', 'Antony')
        );

        $this->assertEqualSQL(
            'SELECT * FROM "users" WHERE "name" LIKE \'%Antony%\'',
            $select
        );
    }

    public function testLikePattern(): void
    {
        $select = $this->initCompiler()->compile(
            $this->initQuery(),
            new Filter\Like('name', 'Antony', '%%%s')
        );

        $this->assertEqualSQL(
            'SELECT * FROM "users" WHERE "name" LIKE \'%Antony\'',
            $select
        );
    }

    public function testAndQuery(): void
    {
        $select = $this->initCompiler()->compile(
            $this->initQuery(),
            new Filter\All(
                new Filter\Equals('name', 'Antony'),
                new Filter\Equals('balance', 100)
            )
        );

        $this->assertEqualSQL(
            'SELECT * FROM "users" WHERE ("name" = \'Antony\' AND "balance" = 100)',
            $select
        );
    }

    public function testOrQuery(): void
    {
        $select = $this->initCompiler()->compile(
            $this->initQuery(),
            new Filter\Any(
                new Filter\Equals('name', 'Antony'),
                new Filter\Equals('balance', 100)
            )
        );

        $this->assertEqualSQL(
            'SELECT * FROM "users" WHERE (("name" = \'Antony\') OR ("balance" = 100))',
            $select
        );
    }

    public function testOrAndOrQuery(): void
    {
        $select = $this->initCompiler()->compile(
            $this->initQuery(),
            new Filter\All(
                new Filter\Any(
                    new Filter\Equals('a', 'aa'),
                    new Filter\Equals('b', 'bb')
                ),
                new Filter\Any(
                    new Filter\Equals('c', 'cc'),
                    new Filter\Equals('d', 'dd')
                )
            )
        );

        $this->assertEqualSQL(
            'SELECT * FROM "users" WHERE ((("a" = \'aa\') OR ("b" = \'bb\')) AND (("c" = \'cc\') OR ("d" = \'dd\')))',
            $select
        );
    }

    public function testSort(): void
    {
        $select = $this->initCompiler()->compile(
            $this->initQuery(),
            new Sorter\AscSorter('balance')
        );

        $this->assertEqualSQL(
            'SELECT * FROM "users"  ORDER BY "balance" ASC',
            $select
        );
    }

    public function testSortDesc(): void
    {
        $select = $this->initCompiler()->compile(
            $this->initQuery(),
            new Sorter\DescSorter('balance')
        );

        $this->assertEqualSQL(
            'SELECT * FROM "users"  ORDER BY "balance" DESC',
            $select
        );
    }

    public function testSortMultiple(): void
    {
        $select = $this->initCompiler()->compile(
            $this->initQuery(),
            new Sorter\AscSorter('balance'),
            new Sorter\AscSorter('credits'),
            new Sorter\DescSorter('attempts')
        );

        $this->assertEqualSQL(
            'SELECT * FROM "users"  ORDER BY "balance" ASC, "credits" ASC, "attempts" DESC',
            $select
        );
    }

    public function testUnary(): void
    {
        $unary = new Sorter\UnarySorter(
            new Sorter\AscSorter('balance'),
            new Sorter\AscSorter('credits'),
            new Sorter\DescSorter('attempts')
        );
        $select = $this->initCompiler()->compile(
            $this->initQuery(),
            $unary
        );

        $this->assertEqualSQL(
            'SELECT * FROM "users"  ORDER BY "balance" ASC, "credits" ASC, "attempts" DESC',
            $select
        );

        $select = $this->initCompiler()->compile(
            $this->initQuery(),
            new Sorter\UnarySorter($unary)
        );

        $this->assertEqualSQL(
            'SELECT * FROM "users"  ORDER BY "balance" ASC, "credits" ASC, "attempts" DESC',
            $select
        );
    }

    /**
     * @dataProvider binarySortProvider
     * @param mixed      $direction
     * @param mixed|null $resultDirection
     */
    public function testBinary($direction, $resultDirection = null): void
    {
        $sorter = new Sorter\BinarySorter(
            new Sorter\UnarySorter(
                new Sorter\AscSorter('balance'),
                new Sorter\AscSorter('credits')
            ), new Sorter\UnarySorter(
                new Sorter\DescSorter('balance'),
                new Sorter\DescSorter('credits')
            )
        );

        if ($resultDirection === null) {
            $this->assertNull($sorter->withDirection($direction));
        } else {
            $select = $this->initCompiler()->compile(
                $this->initQuery(),
                $sorter->withDirection($direction)
            );

            $this->assertEqualSQL(
                sprintf(
                    'SELECT * FROM "users"  ORDER BY "balance" %s, "credits" %s',
                    $resultDirection,
                    $resultDirection
                ),
                $select
            );
        }
    }

    public function testMixedBinary(): void
    {
        $sorter = new Sorter\BinarySorter(
            new Sorter\UnarySorter(
                new Sorter\AscSorter('balance'),
                new Sorter\DescSorter('credits')
            ), new Sorter\UnarySorter(
                new Sorter\DescSorter('balance'),
                new Sorter\AscSorter('credits')
            )
        );

        $select = $this->initCompiler()->compile(
            $this->initQuery(),
            $sorter->withDirection('asc')
        );
        $this->assertEqualSQL(
            'SELECT * FROM "users"  ORDER BY "balance" ASC, "credits" DESC',
            $select
        );

        $select = $this->initCompiler()->compile(
            $this->initQuery(),
            $sorter->withDirection('desc')
        );
        $this->assertEqualSQL(
            'SELECT * FROM "users"  ORDER BY "balance" DESC, "credits" ASC',
            $select
        );
    }

    /**
     * @dataProvider binarySortProvider
     * @param mixed      $direction
     * @param mixed|null $resultDirection
     */
    public function testSortBinary($direction, $resultDirection = null): void
    {
        $sorter = new Sorter\Sorter('balance', 'credits');

        if ($resultDirection === null) {
            $this->assertNull($sorter->withDirection($direction));
        } else {
            $select = $this->initCompiler()->compile(
                $this->initQuery(),
                $sorter->withDirection($direction)
            );

            $this->assertEqualSQL(
                sprintf(
                    'SELECT * FROM "users"  ORDER BY "balance" %s, "credits" %s',
                    $resultDirection,
                    $resultDirection
                ),
                $select
            );
        }
    }

    public function binarySortProvider(): array
    {
        return [
            ['asc', 'ASC'],
            ['1', 'ASC'],
            [1, 'ASC'],
            [SORT_ASC, 'ASC'],

            ['desc', 'DESC'],
            ['-1', 'DESC'],
            [-1, 'DESC'],
            [SORT_DESC, 'DESC'],

            [123, null],
        ];
    }

    public function testInArray(): void
    {
        $select = $this->initCompiler()->compile(
            $this->initQuery(),
            new Filter\InArray('id', [1, 2, 3])
        );

        $this->assertEqualSQL(
            'SELECT * FROM "users" WHERE "id" IN (1,2,3)',
            $select
        );
    }

    public function testNotInArray(): void
    {
        $select = $this->initCompiler()->compile(
            $this->initQuery(),
            new Filter\NotInArray('id', [1, 2, 3])
        );

        $this->assertEqualSQL(
            'SELECT * FROM "users" WHERE "id" NOT IN (1,2,3)',
            $select
        );
    }

    public function testPaginate(): void
    {
        $select = $this->initCompiler()->compile(
            $this->initQuery(),
            (new Pagination\PagePaginator(25))->withValue([])
        );

        $this->assertEqualSQL(
            'SELECT * FROM "users" LIMIT 25',
            $select
        );
    }

    public function testPaginate2(): void
    {
        $select = $this->initCompiler()->compile(
            $this->initQuery(),
            (new Pagination\PagePaginator(25))->withValue(['page' => 2])
        );

        $this->assertEqualSQL(
            'SELECT * FROM "users" LIMIT 25 OFFSET 25',
            $select
        );
    }

    public function testPaginate3(): void
    {
        $select = $this->initCompiler()->compile(
            $this->initQuery(),
            (new Pagination\PagePaginator(25, [50]))->withValue([
                'page'  => '2',
                'limit' => '50'
            ])
        );

        $this->assertEqualSQL(
            'SELECT * FROM "users" LIMIT 50 OFFSET 50',
            $select
        );
    }

    /**
     * @return Compiler
     */
    public function initCompiler(): Compiler
    {
        $compiler = new Compiler();
        $compiler->addWriter(new QueryWriter());

        return $compiler;
    }

    /**
     * @return SelectQuery
     */
    private function initQuery(): SelectQuery
    {
        return (new Database('default', '', new SQLiteDriver([])))->select()->from('users');
    }

    /**
     * @return GridHydrator
     */
    private function initGenerator(): GridHydrator
    {
        return new GridHydrator($this->initCompiler());
    }

    /**
     * @param string      $expected
     * @param SelectQuery $compiled
     */
    private function assertEqualSQL(string $expected, SelectQuery $compiled): void
    {
        $compiled = Interpolator::interpolate(
            $compiled->sqlStatement(),
            $compiled->getParameters()
        );

        $this->assertSame(
            preg_replace("/\s+/", '', $expected),
            preg_replace("/\s+/", '', $compiled)
        );
    }
}
