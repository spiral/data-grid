<?php

declare(strict_types=1);

namespace Spiral\DataGrid\Specification\Filter;

use Spiral\DataGrid\Exception\ValueException;
use Spiral\DataGrid\Specification\FilterInterface;
use Spiral\DataGrid\Specification\ValueInterface;
use Spiral\DataGrid\SpecificationInterface;

/**
 * Filters records where a VALUE falls between two FIELD boundaries.
 * This is the inverse of Between - instead of checking if a field is between two values,
 * it checks if a single value falls between two fields.
 *
 * ```
 * // Job age requirement matching
 * $ageFilter = new ValueBetween(new IntValue(), ['min_age', 'max_age']);
 * $result = $ageFilter->withValue(25); // Find jobs where 25 is between min_age and max_age
 * ```
 * ```
 * // Budget-friendly product search
 * $budgetFilter = new ValueBetween(new NumericValue(), ['min_price', 'max_price']);
 * $result = $budgetFilter->withValue(500); // Products where $500 is within price range
 * ```
 * ```
 * // Event availability checking
 * $dateFilter = new ValueBetween(new DatetimeValue(), ['start_date', 'end_date']);
 * $result = $dateFilter->withValue('2024-06-15'); // Events active on this date
 * ```
 * ```
 * // Fixed value between dynamic fields
 * $currentTimeFilter = new ValueBetween('2024-06-15 14:30:00', ['start_time', 'end_time']);
 * // Find events active at this specific time
 * ```
 * ```
 * // Salary range matching for job seekers
 * $salaryFilter = new ValueBetween(new NumericValue(), ['salary_min', 'salary_max']);
 * $result = $salaryFilter->withValue(75000); // Jobs where $75k is within salary range
 * ```
 * ```
 * // Venue capacity matching
 * $capacityFilter = new ValueBetween(new IntValue(), ['min_capacity', 'max_capacity']);
 * $result = $capacityFilter->withValue(150); // Venues that can accommodate 150 people
 * ```
 * ```
 * // Geographic coordinate checking
 * $latitudeFilter = new ValueBetween(new FloatValue(), ['south_boundary', 'north_boundary']);
 * $result = $latitudeFilter->withValue(40.7128); // Areas containing this latitude
 * ```
 * ```
 * // Software version compatibility
 * $versionFilter = new ValueBetween(new StringValue(), ['min_version', 'max_version']);
 * $result = $versionFilter->withValue('2.1.5'); // Compatible software versions
 * ```
 */
final class ValueBetween implements FilterInterface
{
    /** @var string[] */
    private readonly array $value;

    /**
     * @param ValueInterface|string|int|float $expression The value to check (or ValueInterface for dynamic input)
     * @param string[] $value Array of two field names representing the boundaries [min_field, max_field]
     * @param bool $includeFrom Whether to include the lower boundary (>= vs >)
     * @param bool $includeTo Whether to include the upper boundary (<= vs <)
     */
    public function __construct(
        private ValueInterface|string|int|float $expression,
        array $value,
        private readonly bool $includeFrom = true,
        private readonly bool $includeTo = true,
    ) {
        if (!$this->isValidArray($value)) {
            throw new ValueException(
                \sprintf(
                    'Value expected to be an array of 2 different scalar elements, got %s.',
                    $this->invalidValueType($value),
                ),
            );
        }
        $this->value = \array_values($value);
    }

    public function withValue(mixed $value): ?SpecificationInterface
    {
        $between = clone $this;
        if (!$between->expression instanceof ValueInterface) {
            //constant value
            return $between;
        }

        if (!$between->expression->accepts($value)) {
            return null;
        }

        $between->expression = $between->expression->convert($value);

        return $between;
    }

    public function getValue(): mixed
    {
        return $this->expression;
    }

    /**
     * Get the field names that represent the boundaries.
     *
     * @return string[] Array of two field names [min_field, max_field]
     */
    public function getExpression(): array
    {
        return $this->value;
    }

    /**
     * Get the filters as separate conditions for compatibility with systems that don't support BETWEEN.
     *
     * @param bool $asOriginal If true and boundaries are inclusive, returns this filter as-is
     * @return SpecificationInterface[] Array of filter specifications
     */
    public function getFilters(bool $asOriginal = false): array
    {
        if ($asOriginal && $this->includeFrom && $this->includeTo) {
            return [$this];
        }

        return [$this->fromFilter(), $this->toFilter()];
    }

    private function isValidArray(array $value): bool
    {
        if (\count($value) !== 2) {
            return false;
        }

        [$from, $to] = \array_values($value);

        return \is_scalar($from) && \is_scalar($to) && $from !== $to;
    }

    private function invalidValueType(array $value): string
    {
        $count = \count($value);
        if ($count === 0) {
            return 'empty array';
        }

        if ($count !== 2) {
            return \sprintf('array of %s elements', $count);
        }

        [$from, $to] = \array_values($value);
        if (!\is_scalar($from) || !\is_scalar($to)) {
            return 'array of 2 not scalar elements';
        }

        return 'array of 2 same elements';
    }

    private function fromFilter(): FilterInterface
    {
        return $this->includeFrom
            ? new Gte($this->value[1], $this->expression)
            : new Gt($this->value[1], $this->expression);
    }

    private function toFilter(): FilterInterface
    {
        return $this->includeTo
            ? new Lte($this->value[0], $this->expression)
            : new Lt($this->value[0], $this->expression);
    }
}
