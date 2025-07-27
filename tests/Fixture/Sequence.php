<?php

/**
 * Spiral Framework. PHP Data Grid
 *
 * @license MIT
 * @author  Anton Tsitou (Wolfy-J)
 * @author  Valentin Vintsukevich (vvval)
 */

declare(strict_types=1);

namespace Spiral\Tests\DataGrid\Fixture;

use Spiral\DataGrid\Specification\SequenceInterface;
use Spiral\DataGrid\SpecificationInterface;

/**
 * Mocks public value and set of underlying specifications.
 */
class Sequence implements SequenceInterface
{
    /** @var SpecificationInterface[] */
    private $specifications;

    public function __construct(private readonly array $value, SpecificationInterface ...$specifications)
    {
        $this->specifications = $specifications;
    }

    /**
     * @return SpecificationInterface[]
     */
    public function getSpecifications(): array
    {
        return $this->specifications;
    }

    public function getValue(): array
    {
        return $this->value;
    }
}
