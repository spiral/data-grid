<?php

/**
 * Spiral Framework. PHP Data Grid
 *
 * @author Valentin Vintsukevich (vvval)
 */

declare(strict_types=1);

namespace Spiral\DataGrid\Specification\Filter;

use Spiral\DataGrid\Exception\ValueException;
use Spiral\DataGrid\Specification\FilterInterface;
use Spiral\DataGrid\Specification\ValueInterface;
use Spiral\DataGrid\SpecificationInterface;

final class Between implements FilterInterface
{
    /** @var string|int|float|bool|null|ValueInterface */
    private $expression;

    /** @var ValueInterface|array */
    private $value;

    /** @var bool */
    private $includeFrom;

    /** @var bool */
    private $includeTo;

    /** @var bool */
    private $viaExpression;

    /**
     * @param ValueInterface|string|int|float|bool|null $expression
     * @param ValueInterface|array                      $value
     * @param bool                                      $includeFrom
     * @param bool                                      $includeTo
     */
    public function __construct($expression, $value, bool $includeFrom = true, bool $includeTo = true)
    {
        if ($expression instanceof ValueInterface) {
            //got value between 2 fields
            if (!$this->isValidArray($value)) {
                throw new ValueException(sprintf(
                    'Value expected to be an array of 2 different elements, got %s.',
                    $this->invalidValueType($value)
                ));
            }
        } elseif (!$value instanceof ValueInterface && !$this->isValidArray($value)) {
            //got field between 2 values
            throw new ValueException(sprintf(
                'Value expected to be instance of `%s` or an array of 2 different elements, got %s.',
                ValueInterface::class,
                $this->invalidValueType($value)
            ));
        }

        $this->expression = $expression;
        $this->value = $this->convertValue($value);
        $this->includeFrom = $includeFrom;
        $this->includeTo = $includeTo;
        $this->viaExpression = $expression instanceof ValueInterface;
    }

    /**
     * @inheritDoc
     * @return self|SpecificationInterface|null
     */
    public function withValue($value): ?SpecificationInterface
    {
        $between = clone $this;

        if ($between->viaExpression) {
            if (!$between->expression instanceof ValueInterface) {
                //constant value
                return $between;
            }

            if (!$between->expression->accepts($value)) {
                return null;
            }

            $between->expression = $between->expression->convert($value);
        } else {
            if (!$between->value instanceof ValueInterface) {
                // constant value
                return $between;
            }

            if (!$this->isValidArray($value)) {
                // only array of 2 values is expected
                return null;
            }

            [$from, $to] = $this->convertValue($value);
            if (!$between->value->accepts($from) || !$between->value->accepts($to)) {
                return null;
            }

            $between->value = [$from, $to];
        }

        return $between;
    }

    /**
     * @inheritDoc
     * @return ValueInterface|array|string|int|float|bool|null
     */
    public function getValue()
    {
        return $this->viaExpression ? $this->expression : $this->value;
    }

    /**
     * @param bool $asOriginal
     * @return SpecificationInterface[]
     */
    public function getFilters(bool $asOriginal = false): array
    {
        if ($asOriginal && $this->includeFrom && $this->includeTo) {
            return [$this];
        }

        return (new All($this->fromFilter(), $this->toFilter()))->getFilters();
    }


    /**
     * @param mixed|array $value
     * @return bool
     */
    private function isValidArray($value): bool
    {
        if (!is_array($value) || count($value) !== 2) {
            return false;
        }

        [$from, $to] = array_values($value);

        return $from !== $to;
    }

    /**
     * @param mixed $value
     * @return string
     */
    private function invalidValueType($value): string
    {
        if (is_object($value)) {
            return get_class($value);
        }

        if (is_array($value)) {
            return count($value) === 2 ? 'array of 2 same elements' : 'array of ' . count($value) . ' elements';
        }

        return gettype($value);
    }

    /**
     * @param ValueInterface|array $value
     * @return ValueInterface|array
     */
    private function convertValue($value)
    {
        if ($value instanceof ValueInterface) {
            return $value;
        }

        $values = array_values($value);
        if ($values[1] < $values[0]) {
            return [$values[1], $values[0]];
        }

        return $values;
    }

    /**
     * @return FilterInterface
     */
    private function fromFilter(): FilterInterface
    {
        if ($this->viaExpression) {
            return $this->includeFrom
                ? new Gte($this->value[0], $this->expression)
                : new Gt($this->value[0], $this->expression);
        }

        $value = $this->value instanceof ValueInterface ? $this->value : $this->value[0];

        return $this->includeFrom
            ? new Gte($this->expression, $value)
            : new Gt($this->expression, $value);
    }

    /**
     * @return FilterInterface
     */
    private function toFilter(): FilterInterface
    {
        if ($this->viaExpression) {
            return $this->includeTo
                ? new Lte($this->value[1], $this->expression)
                : new Lt($this->value[1], $this->expression);
        }

        $value = $this->value instanceof ValueInterface ? $this->value : $this->value[1];

        return $this->includeTo
            ? new Lte($this->expression, $value)
            : new Lt($this->expression, $value);
    }
}
