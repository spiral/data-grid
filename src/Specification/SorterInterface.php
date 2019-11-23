<?php

/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Valentin Vintsukevich (vvval)
 * @author    Anton Tsitou (Wolfy-J)
 */

declare(strict_types=1);

namespace Spiral\DataGrid\Specification;

use Spiral\DataGrid\SpecificationInterface;

/**
 * The specification to configure the sorting direction of the data source.
 */
interface SorterInterface extends SpecificationInterface
{
    // available directions
    public const ASC  = 'asc';
    public const DESC = 'desc';

    /**
     * Lock the sorter to the specific sorting direction.
     *
     * @param mixed $direction
     * @return SorterInterface|null
     */
    public function withDirection($direction): ?SpecificationInterface;
}
