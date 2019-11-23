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
use Spiral\DataGrid\Specification\Pagination\PagePaginator;
use Spiral\DataGrid\Specification\Sequence;

class PaginatorTest extends TestCase
{
    public function testLimitPaginator(): void
    {
        $p = new PagePaginator(25, [25, 50, 100]);

        $this->assertInstanceOf(Sequence::class, $p->withValue(null));
        $this->assertSame([
            'limit' => 25,
            'page'  => 1
        ], $p->withValue(null)->getValue());

        $this->assertInstanceOf(Sequence::class, $p->withValue([
            'page' => 1
        ]));

        $this->assertInstanceOf(Sequence::class, $p->withValue([
            'page' => 2
        ]));

        $this->assertSame([
            'limit' => 100,
            'page'  => 2
        ], $p->withValue(['page' => 2, 'limit' => 100])->getValue());
    }
}
