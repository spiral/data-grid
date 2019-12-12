<?php

/**
 * Spiral Framework. PHP Data Grid
 *
 * @author Valentin Vintsukevich (vvval)
 */

declare(strict_types=1);

namespace Spiral\Tests\DataGrid\Fixture;

use Spiral\DataGrid\Compiler;
use Spiral\DataGrid\Specification\SequenceInterface;
use Spiral\DataGrid\SpecificationInterface;
use Spiral\DataGrid\WriterInterface;

class SequenceWriter implements WriterInterface
{
    /**
     * {@inheritDoc}
     */
    public function write($source, SpecificationInterface $sequence, Compiler $compiler)
    {
        if (is_array($source) && $sequence instanceof SequenceInterface) {
            foreach ($sequence->getSpecifications() as $specification) {
                $source[] = get_class($specification);
            }
        }

        return $source;
    }
}
