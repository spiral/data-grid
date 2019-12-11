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
use Spiral\DataGrid\Specification\Filter\Equals;
use Spiral\DataGrid\Specification\Filter\Gte;
use Spiral\DataGrid\Specification\Filter\Lte;
use Spiral\DataGrid\Specification\Filter\Map;
use Spiral\DataGrid\Specification\FilterInterface;
use Spiral\DataGrid\Specification\Value\IntValue;

class FilterTest extends TestCase
{
    public function testEquals(): void
    {
        $e = new Equals('field', 1);

        $this->assertSame(1, $e->getValue());
    }

    public function testEqualsApply(): void
    {
        $e = new Equals('field', new IntValue());

        $this->assertInstanceOf(IntValue::class, $e->getValue());

        $e = $e->withValue('10');
        $this->assertNotNull($e);

        $this->assertSame(10, $e->getValue());
    }

    public function testComplexFilter(): void
    {
        $c = new Map([
            'from' => new Gte('balance', new IntValue()),
            'to'   => new Lte('balance', new IntValue()),
        ]);

        $this->assertNull($c->withValue(null));
        $this->assertNull($c->withValue('scalar'));

        $this->assertNull($c->withValue([]));
        $this->assertNull($c->withValue(['from' => 1]));

        $all = $c->withValue(['from' => 1, 'to' => 2]);
        $this->assertInstanceOf(Map::class, $all);

        /**
         * @var FilterInterface $from
         * @var FilterInterface $to
         */
        ['from' => $from, 'to' => $to] = $all->getFilters();


        $this->assertSame(1, $from->getValue());
        $this->assertSame(2, $to->getValue());
    }
}
