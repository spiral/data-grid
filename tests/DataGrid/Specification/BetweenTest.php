<?php

/**
 * Spiral Framework. PHP Data Grid
 *
 * @author Valentin Vintsukevich (vvval)
 */

declare(strict_types=1);

namespace Spiral\Tests\DataGrid\Specification;

use PHPUnit\Framework\TestCase;
use Spiral\DataGrid\Exception\ValueException;
use Spiral\DataGrid\Specification\Filter;
use Spiral\DataGrid\Specification\Value\BoolValue;
use Spiral\DataGrid\Specification\Value\IntValue;

class BetweenTest extends TestCase
{
    /**
     * @dataProvider initValueProvider
     * @param mixed       $value
     * @param string|null $exception
     */
    public function testInitValue($value, ?string $exception): void
    {
        $this->assertTrue(true);
        if ($exception !== null) {
            $this->expectException($exception);
        }

        new Filter\Between('field', $value, false, false);
    }

    /**
     * @return iterable
     */
    public function initValueProvider(): iterable
    {
        return [
            [new IntValue(), null],
            [[1, 2], null],
            [[3, 2], null],
            ['string', ValueException::class],
            [[], ValueException::class],
            [[1], ValueException::class],
            [[2, 2], ValueException::class],
            [[1, 2, 3], ValueException::class],
        ];
    }

    /**
     * @dataProvider valueValidationProvider
     * @param mixed $value
     * @param mixed $with
     * @param mixed $valid
     */
    public function testValueValidation($value, $with, bool $valid): void
    {
        $between = new Filter\Between('field', $value, false, false);
        $between = $between->withValue($with);

        $this->assertEquals($valid, $between !== null);
    }

    /**
     * @return iterable
     */
    public function valueValidationProvider(): iterable
    {
        $incorrectValues = [
            'string',
            new IntValue(),
            1,
            [],
            [1],
            [1, 2, 3]
        ];

        foreach ($incorrectValues as $incorrectValue) {
            yield[[1, 2], $incorrectValue, true];
            yield[new IntValue(), $incorrectValue, false];
        }

        yield [new BoolValue(), [1, 2], false];

        return [
            [[1, 2], [2, 3], true],
            [new IntValue(), [2, 3], true],
            [new IntValue(), [3, 2], true],
        ];
    }

    /**
     * @dataProvider includeProvider
     * @param bool   $includeFrom
     * @param bool   $includeTo
     * @param string $from
     * @param string $to
     */
    public function testInclude(bool $includeFrom, bool $includeTo, string $from, string $to): void
    {
        $between = new Filter\Between('field', new IntValue(), $includeFrom, $includeTo);
        $between = $between->withValue([2, 3]);
        $filters = $between->getFilters();

        $this->assertNotEmpty($filters);
        $this->assertInstanceOf($from, $filters[0]);
        $this->assertInstanceOf($to, $filters[1]);
    }

    /**
     * @return iterable
     */
    public function includeProvider(): iterable
    {
        return [
            [false, false, Filter\Gt::class, Filter\Lt::class],
            [true, false, Filter\Gte::class, Filter\Lt::class],
            [false, true, Filter\Gt::class, Filter\Lte::class],
            [true, true, Filter\Gte::class, Filter\Lte::class],
        ];
    }

    /**
     * @dataProvider swapBoundariesProvider
     * @param Filter\Between $between
     */
    public function testSwapBoundaries(Filter\Between $between): void
    {
        $filters = $between->getFilters();

        $this->assertEquals(2, $filters[0]->getValue());
        $this->assertEquals(3, $filters[1]->getValue());
    }

    /**
     * @return iterable
     */
    public function swapBoundariesProvider(): iterable
    {
        yield [new Filter\Between('field', [3, 2])];

        $between = new Filter\Between('field', new IntValue());
        yield [$between->withValue([3, 2])];
    }
}
