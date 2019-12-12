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

use PHPUnit\Framework\TestCase;
use Spiral\DataGrid\Specification\Pagination\PagePaginator;

class PaginatorTest extends TestCase
{
    /**
     * @dataProvider getValueProvider
     * @param array $expected
     * @param       $value
     */
    public function testLimitPaginator(array $expected, $value): void
    {
        $p = new PagePaginator(25, [50, 100]);
        $this->assertSame($expected, $p->withValue($value)->getValue());
    }

    /**
     * @return iterable
     */
    public function getValueProvider(): iterable
    {
        return [
            [['limit' => 25, 'page' => 1], null],
            [['limit' => 25, 'page' => 2], ['page' => 2]],
            [['limit' => 100, 'page' => 2], ['page' => 2, 'limit' => 100]],
            [['limit' => 100, 'page' => 1], ['limit' => 100]],
        ];
    }

    /**
     * @return iterable
     */
    public function withValueProvider(): iterable
    {
        return [
            [null],
            ['page' => 1],
            ['page' => 1, 'limit' => 50],
            ['limit' => 50],
        ];
    }
}
