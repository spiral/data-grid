<?php

/**
 * Spiral Framework. PHP Data Grid
 *
 * @author Valentin Vintsukevich (vvval)
 */

declare(strict_types=1);

namespace Spiral\DataGrid\Specification\Value;

use Spiral\DataGrid\Exception\ValueException;
use Spiral\DataGrid\Specification\ValueInterface;

/**
 * @example new Gt('field', new RangeValue(new IntValue(), null, RangeValue\Boundary::excluding(12)))
 * will mean that 'field' value should be greater than the input but only if the input is less than 12.
 */
final class RangeValue implements ValueInterface
{
    /** @var ValueInterface */
    private $base;

    /** @var RangeValue\Boundary */
    private $start;

    /** @var RangeValue\Boundary */
    private $end;

    public function __construct(
        ValueInterface $base,
        RangeValue\Boundary $start = null,
        RangeValue\Boundary $end = null
    ) {
        $this->base = $base;
        $start = $start ?? RangeValue\Boundary::empty();
        $end = $end ?? RangeValue\Boundary::empty();

        $this->validateBoundaries($start, $end);
        $this->setBoundaries($start, $end);
    }

    /**
     * @inheritDoc
     */
    public function accepts($value): bool
    {
        return $this->base->accepts($value) && $this->acceptsStart($value) & $this->acceptsEnd($value);
    }

    /**
     * @inheritDoc
     */
    public function convert($value)
    {
        return $this->base->convert($value);
    }

    /**
     * @param RangeValue\Boundary $start
     * @param RangeValue\Boundary $end
     */
    private function validateBoundaries(RangeValue\Boundary $start, RangeValue\Boundary $end): void
    {
        if (!$this->acceptsBoundary($start) || !$this->acceptsBoundary($end)) {
            throw new ValueException('Range boundaries should be applicable via passed type.');
        }

        if ($this->convertBoundaryValue($start) === $this->convertBoundaryValue($end)) {
            throw new ValueException('Range boundaries should be different.');
        }
    }

    /**
     * @param RangeValue\Boundary $boundary
     * @return bool
     */
    private function acceptsBoundary(RangeValue\Boundary $boundary): bool
    {
        return $boundary->empty || $this->base->accepts($boundary->value);
    }

    /**
     * @param RangeValue\Boundary $boundary
     * @return mixed|null
     */
    private function convertBoundaryValue(RangeValue\Boundary $boundary)
    {
        return $boundary->empty ? null : $this->base->convert($boundary->value);
    }

    /**
     * @param mixed $value
     * @return bool
     */
    private function acceptsStart($value): bool
    {
        if ($this->start->empty) {
            return true;
        }

        $startValue = $this->base->convert($this->start->value);

        return $this->start->include ? ($value >= $startValue) : ($value > $startValue);
    }

    /**
     * @param mixed $value
     * @return bool
     */
    private function acceptsEnd($value): bool
    {
        if ($this->end->empty) {
            return true;
        }

        $endValue = $this->base->convert($this->end->value);

        return $this->end->include ? ($value <= $endValue) : ($value < $endValue);
    }

    /**
     * @param RangeValue\Boundary $start
     * @param RangeValue\Boundary $end
     */
    private function setBoundaries(RangeValue\Boundary $start, RangeValue\Boundary $end): void
    {
        //Swap if start < end and both not empty
        if (!$start->empty && !$end->empty && $start->value > $end->value) {
            [$start, $end] = [$end, $start];
        }

        $this->start = $start;
        $this->end = $end;
    }
}
