<?php

/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Valentin Vintsukevich (vvval), Anton Titov (Wolfy-J)
 */

declare(strict_types=1);

namespace Spiral\DataGrid\Specification;

use Spiral\DataGrid\SpecificationInterface;

/**
 * Mocks public value and set of underlying specifications.
 */
final class Sequence implements SequenceInterface
{
    /** @var array */
    private $value;

    /** @var SpecificationInterface[] */
    private $specifications;

    /**
     * @param array                  $value
     * @param SpecificationInterface ...$specifications
     */
    public function __construct(array $value, SpecificationInterface ...$specifications)
    {
        $this->value = $value;
        $this->specifications = $specifications;
    }

    /**
     * @return SpecificationInterface[]
     */
    public function getSpecifications(): array
    {
        return $this->specifications;
    }

    /**
     * @return array
     */
    public function getValue(): array
    {
        return $this->value;
    }
}
