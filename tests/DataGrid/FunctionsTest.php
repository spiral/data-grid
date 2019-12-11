<?php

/**
 * Spiral Framework. PHP Data Grid
 *
 * @author Valentin Vintsukevich (vvval)
 */

declare(strict_types=1);

namespace Spiral\Tests\DataGrid;

use LogicException;
use PHPUnit\Framework\TestCase;

use function Spiral\DataGrid\getValue;
use function Spiral\DataGrid\hasKey;

class FunctionsTest extends TestCase
{
    /**
     * @dataProvider hasKeyProvider
     * @param string $key
     * @param bool   $expected
     */
    public function testHasKey(string $key, bool $expected): void
    {
        $data = [
            'key1' => 'value1',
            'Key2' => 'value2',
        ];

        $this->assertSame($expected, hasKey($data, $key));
    }

    /**
     * @return iterable
     */
    public function hasKeyProvider(): iterable
    {
        return [
            ['key1', true],
            ['kEy1', true],
            ['key 1', false],
            ['key2', true],
            ['Key2', true],
            ['keY2', true],
        ];
    }

    /**
     * @dataProvider getValueProvider
     * @param string      $key
     * @param string|null $expectException
     * @param             $expected
     */
    public function testGetValue(string $key, ?string $expectException, $expected): void
    {
        $data = [
            'key1' => 'value1',
            'Key2' => 'value2',
        ];

        if ($expectException !== null) {
            $this->expectException($expectException);
        }

        $this->assertSame($expected, getValue($data, $key));
    }

    /**
     * @return iterable
     */
    public function getValueProvider(): iterable
    {
        return [
            ['key1', null, 'value1'],
            ['kEy1', null, 'value1'],
            ['key 1', LogicException::class, null],
            ['key2', null, 'value2'],
            ['Key2', null, 'value2'],
            ['keY2', null, 'value2'],
        ];
    }
}
