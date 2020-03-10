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
use Spiral\DataGrid\Specification\ValueInterface;
use stdClass;

class BetweenTest extends TestCase
{
    /**
     * @dataProvider initValueProvider
     * @param mixed       $expression
     * @param mixed       $value
     * @param string|null $exception
     */
    public function testInitValue($expression, $value, ?string $exception): void
    {
        $this->assertTrue(true);
        if ($exception !== null) {
            $this->expectException($exception);
        }

        new Filter\Between($expression, $value, false, false);
    }

    /**
     * @return iterable
     */
    public function initValueProvider(): iterable
    {
        $expressions = ['field', new IntValue()];

        $invalidValues = [
            new stdClass(),
            [],
            [1],
            [2, 2],
            [1, 2, 3],
        ];

        foreach ($expressions as $expression) {
            foreach ($invalidValues as $invalidValue) {
                yield [$expression, $invalidValue, ValueException::class];
            }
        }

        yield from [
            ['field', new IntValue(), null],
            ['field', [1, 2], null],
            ['field', [3, 2], null],
            [new IntValue(), [1, 2], null],
            [new IntValue(), [new IntValue(), new IntValue()], null],

            ['field', 'string', ValueException::class],
            [new IntValue(), new IntValue(), ValueException::class],
            [new IntValue(), 'field', ValueException::class],
        ];
    }

    /**
     * @dataProvider withValueValidationProvider
     * @param mixed $expression
     * @param mixed $value
     * @param mixed $withValue
     * @param mixed $valid
     */
    public function testWithValueValidation($expression, $value, $withValue, bool $valid): void
    {
        $between = new Filter\Between($expression, $value, false, false);
        $between = $between->withValue($withValue);

        if ($valid) {
            $this->assertNotNull($between);
            $this->assertEquals(
                $expression instanceof ValueInterface ? $withValue : $value,
                $between->getValue()
            );
        } else {
            $this->assertNull($between);
        }
    }

    /**
     * @return iterable
     */
    public function withValueValidationProvider(): iterable
    {
        $incorrectValues = [
            'string',
            new IntValue(),
            true,
            false,
            null,
            [],
            [1],
            [1, 2, 3],
            new stdClass()
        ];

        foreach ($incorrectValues as $incorrectValue) {
            yield['field', [1, 2], $incorrectValue, true];
            yield['field', new IntValue(), $incorrectValue, false];
            yield[new IntValue(), [1, 2], $incorrectValue, false];
        }

        yield from [
            [new IntValue(), [1, 2], 1, true],
            ['field', new BoolValue(), [1, 2], false],
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

    /**
     * @dataProvider originalProvider
     * @param Filter\Between $between
     * @param bool           $isOriginal
     * @param string|null    $from
     * @param string|null    $to
     */
    public function testOriginal(
        Filter\Between $between,
        bool $isOriginal,
        ?string $from,
        ?string $to
    ): void {
        $filters = $between->getFilters(true);

        if ($isOriginal) {
            $this->assertCount(1, $filters);
            $this->assertInstanceOf(Filter\Between::class, $filters[0]);
        } else {
            $this->assertCount(2, $filters);
            $this->assertInstanceOf($from, $filters[0]);
            $this->assertInstanceOf($to, $filters[1]);
        }
    }

    /**
     * @return iterable
     */
    public function originalProvider(): iterable
    {
        yield from [
            [new Filter\Between('field', new IntValue()), true, null, null],
            [new Filter\Between('field', [1, 2], false), false, Filter\Gt::class, Filter\Lte::class],
            [new Filter\Between('field', [1, 2], true, false), false, Filter\Gte::class, Filter\Lt::class],
            [new Filter\Between('field', [1, 2], false, false), false, Filter\Gt::class, Filter\Lt::class],
        ];

        yield from [
            [new Filter\Between(new IntValue(), ['field1', 'field2']), true, null, null],
            [
                new Filter\Between(new IntValue(), ['field1', 'field2'], false),
                false,
                Filter\Gt::class,
                Filter\Lte::class
            ],
            [
                new Filter\Between(new IntValue(), ['field1', 'field2'], true, false),
                false,
                Filter\Gte::class,
                Filter\Lt::class
            ],
            [
                new Filter\Between(new IntValue(), ['field1', 'field2'], false, false),
                false,
                Filter\Gt::class,
                Filter\Lt::class
            ],
        ];
    }

    public function testGetValue(): void
    {
        $between = new Filter\Between('field', [1, 2]);
        $this->assertIsArray($between->getValue());
        $this->assertIsArray($between->withValue([3, 4])->getValue());

        $between = new Filter\Between('field', new IntValue());
        $this->assertInstanceOf(IntValue::class, $between->getValue());
        $this->assertIsArray($between->withValue([3, 4])->getValue());

        $between = new Filter\Between(new IntValue(), ['field1', 'field2']);
        $this->assertInstanceOf(IntValue::class, $between->getValue());
        $this->assertIsInt($between->withValue(3)->getValue());
    }
}
