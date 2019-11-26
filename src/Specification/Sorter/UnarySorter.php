<?php
/**
 * Drewaltizer
 *
 * @author  Valentin V (vvval)
 */
declare(strict_types=1);

namespace Spiral\DataGrid\Specification\Sorter;

use Spiral\DataGrid\Specification\SorterInterface;
use Spiral\DataGrid\Specification\Value\StringValue;
use Spiral\DataGrid\SpecificationInterface;

class UnarySorter implements SorterInterface
{
    /** @var SorterInterface[] */
    private $sorters;

    /**
     * @param SorterInterface ...$sorters
     */
    public function __construct(SorterInterface ...$sorters)
    {
        $this->sorters = $sorters;
    }

    /**
     * @inheritDoc
     */
    public function withDirection($direction): SpecificationInterface
    {
        return $this;
    }

    /**
     * @return SorterInterface[]
     */
    public function getSorters(): array
    {
        return $this->sorters;
    }

    /**
     * @inheritDoc
     */
    public function getValue()
    {
        return new StringValue();
    }
}
