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
use Spiral\DataGrid\Specification\Value\IntValue;

class ValueTest extends TestCase
{
    public function testInt(): void
    {
        $int = new IntValue();

        $this->assertTrue($int->accepts(0));
        $this->assertTrue($int->accepts('0'));
        $this->assertTrue($int->accepts(1));
        $this->assertTrue($int->accepts('1'));
        $this->assertTrue($int->accepts('1.1'));
        $this->assertTrue($int->accepts(1.1));
        $this->assertFalse($int->accepts([]));

        $this->assertSame(0, $int->convert(0));
        $this->assertSame(0, $int->convert('0'));
        $this->assertSame(1, $int->convert(1));
        $this->assertSame(1, $int->convert('1'));
        $this->assertSame(1, $int->convert('1.1'));
        $this->assertSame(1, $int->convert(1.1));
    }

    // todo: test all boolean variations
}
