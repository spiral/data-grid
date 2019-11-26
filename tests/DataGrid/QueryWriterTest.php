<?php

/**
 * Spiral Framework. PHP Data Grid
 *
 * @license MIT
 * @author  Anton Tsitou (Wolfy-J)
 * @author  Valentin Vintsukevich (vvval)
 */

declare(strict_types=1);

namespace Spiral\Tests\DataGrid;

use Spiral\DataGrid\Specification\Filter;
use Spiral\DataGrid\Specification\Pagination;
use Spiral\Tests\DataGrid\QueryWriter\BaseTest;

class QueryWriterTest extends BaseTest
{
    public function testLimit(): void
    {
        $select = $this->compile(
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
        $select = $this->compile(
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
        $select = $this->compile(
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
        $select = $this->compile(
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
        $select = $this->compile(
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
        $select = $this->compile(
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
        $select = $this->compile(
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
        $select = $this->compile(
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


    public function testInArray(): void
    {
        $select = $this->compile(
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
        $select = $this->compile(
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
        $select = $this->compile(
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
        $select = $this->compile(
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
        $select = $this->compile(
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
}
