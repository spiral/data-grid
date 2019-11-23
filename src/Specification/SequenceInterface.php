<?php

/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Valentin V (vvval), Anton Titov (Wolfy-J)
 */

declare(strict_types=1);

namespace Spiral\DataGrid\Specification;

use Spiral\DataGrid\SpecificationInterface;

/**
 * Sequence interface provides the ability to wrap multiple specifications inside one object.
 */
interface SequenceInterface extends SpecificationInterface
{
    /**
     * Return underlying specifications.
     *
     * @return SpecificationInterface[]
     */
    public function getSpecifications(): array;
}
