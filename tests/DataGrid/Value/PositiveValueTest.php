<?php

/**
 * Spiral Framework. PHP Data Grid
 *
 * @license MIT
 * @author  Anton Tsitou (Wolfy-J)
 * @author  Valentin Vintsukevich (vvval)
 */

declare(strict_types=1);

namespace Spiral\Tests\DataGrid\Value;

use PHPUnit\Framework\TestCase;
use Spiral\DataGrid\Specification\Value\IntValue;
use Spiral\DataGrid\Specification\Value\PositiveValue;

class PositiveValueTest extends TestCase
{
    /**
     * @dataProvider acceptsProvider
     * @param mixed $value
     * @param bool  $expected
     */
    public function testAccepts($value, bool $expected): void
    {
        $int = new PositiveValue(new IntValue());
        $this->assertSame($expected, $int->accepts($value));
    }

    /**
     * @return array
     */
    public function acceptsProvider(): array
    {
        return [
            [1, true],
            ['1', true],
            [1.1, true],
            ['1.1', true],

            [0, false],
            ['0', false],
            [-1, false],
            ['-1', false],
            [-1.1, false],
            ['-1.1', false],
            ['', false],
        ];
    }
}
