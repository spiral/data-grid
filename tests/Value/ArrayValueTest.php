<?php

/**
 * Spiral Framework. PHP Data Grid
 *
 * @license MIT
 * @author  Valentin Vintsukevich (vvval)
 */

declare(strict_types=1);

namespace Spiral\Tests\DataGrid\Value;

use PHPUnit\Framework\TestCase;
use Spiral\DataGrid\Specification\Value;

class ArrayValueTest extends TestCase
{
    /**
     * @dataProvider acceptsProvider
     * @param mixed $value
     */
    public function testAccepts($value, bool $expected): void
    {
        $array = new Value\ArrayValue(new Value\IntValue());
        $this->assertSame($expected, $array->accepts($value));
    }

    /**
     * @dataProvider acceptsProvider
     * @param mixed $value
     */
    public function testAcceptsNested($value, bool $expected): void
    {
        $array = new Value\ArrayValue(new Value\ArrayValue(new Value\IntValue()));
        $this->assertSame($expected, $array->accepts($value));
    }

    public function acceptsProvider(): iterable
    {
        return [
            [[0], true],
            [['0'], true],
            [[1], true],
            [['1'], true],
            [[1.1], true],
            [['1.1'], true],
            [[-2], true],
            [['-2'], true],
            [[-2.2], true],
            [['-2.2'], true],
            [[''], true],

            [false, false],
            ['false', false],
            [true, false],
            ['true', false],
            [null, false],
            ['null', false],
            [new \stdClass(), false],

            [[false], false],
            [['false'], false],
            [[true], false],
            [['true'], false],
            [[null], false],
            [['null'], false],
            [[], false],
            [[new \stdClass()], false],
        ];
    }

    /**
     * @dataProvider convertProvider
     */
    public function testConvert(array $value, array $expected): void
    {
        $array = new Value\ArrayValue(new Value\IntValue());
        $this->assertSame($expected, $array->convert($value));
    }

    /**
     * @dataProvider convertProvider
     */
    public function testConvertNested(array $value, array $expected): void
    {
        $array = new Value\ArrayValue(new Value\ArrayValue(new Value\IntValue()));
        $this->assertSame($expected, $array->convert($value));
    }

    public function convertProvider(): iterable
    {
        return [
            [[0], [0]],
            [['0'], [0]],
            [[1], [1]],
            [['1'], [1]],
            [[1.1], [1]],
            [['1.1'], [1]],
            [[-2], [-2]],
            [['-2'], [-2]],
            [[-2.2], [-2]],
            [['-2.2'], [-2]],
            [[''], [0]],
        ];
    }
}
