<?php

/**
 * Spiral Framework. PHP Data Grid
 *
 * @author Valentin Vintsukevich (vvval)
 */

declare(strict_types=1);

namespace Spiral\DataGrid\Specification;

use Spiral\DataGrid\SpecificationInterface;

class SequentialSpecification implements SequenceInterface
{
    /** @var SpecificationInterface[] */
    private $specifications;

    /**
     * @param SpecificationInterface ...$specifications
     */
    public function __construct(SpecificationInterface ...$specifications)
    {
        $this->specifications = $specifications;
    }

    /**
     * @inheritdoc
     */
    public function getSpecifications(): array
    {
        return $this->specifications;
    }

    /**
     * @inheritdoc
     * @return string
     */
    public function getValue(): string
    {
        return '1';
    }
}
