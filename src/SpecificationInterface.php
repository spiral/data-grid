<?php

/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Valentin Vintsukevich (vvval), Anton Titov (Wolfy-J)
 */

declare(strict_types=1);

namespace Spiral\DataGrid;

/**
 * Free form object which used to limit, sort of configure the target data source.
 */
interface SpecificationInterface
{
    /**
     * Returns public value of the specification. Values of type ValueInterface must be filled by used, while
     * scalar and other values can be delivered to client as current view state.
     *
     * @return mixed
     */
    public function getValue();
}
