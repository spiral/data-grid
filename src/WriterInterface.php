<?php

/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Valentin Vintsukevich (vvval)
 * @author    Anton Tsitou (Wolfy-J)
 */

declare(strict_types=1);

namespace Spiral\DataGrid;

use Spiral\DataGrid\Exception\SpecificationException;

/**
 * Provides the ability to write the specification to a given source.
 */
interface WriterInterface
{
    /**
     * Render the specification and return altered source or null if specification can not be applied.
     *
     * @param mixed                  $source
     * @param SpecificationInterface $specification
     * @param Compiler               $compiler
     * @return mixed|null
     *
     * @throws SpecificationException
     */
    public function write($source, SpecificationInterface $specification, Compiler $compiler);
}
