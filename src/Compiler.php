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

use Spiral\DataGrid\Exception\CompilerException;
use Spiral\DataGrid\Specification\SequenceInterface;

/**
 * SpecificationWriter writes the specifications into target source using a set of associated compilers.
 */
final class Compiler
{
    /** @var WriterInterface[] */
    private $writers = [];

    /**
     * @param WriterInterface $writer
     */
    public function addWriter(WriterInterface $writer): void
    {
        $this->writers[] = $writer;
    }

    /**
     * Compile the source constrains based on a given specification. Returns altered source.
     *
     * @param mixed                  $source
     * @param SpecificationInterface ...$specifications
     * @return mixed|null
     *
     * @throws CompilerException
     */
    public function compile($source, SpecificationInterface ...$specifications)
    {
        foreach ($specifications as $specification) {
            if ($specification instanceof SequenceInterface) {
                return $this->compile($source, ...$specification->getSpecifications());
            }

            foreach ($this->writers as $writer) {
                $result = $writer->write($source, $specification, $this);
                if ($result !== null) {
                    $source = $result;
                    continue 2;
                }
            }

            throw new CompilerException(sprintf(
                'Unable to compile specification `%s` for `%s`, no compiler found',
                get_class($specification),
                is_object($source) ? get_class($source) : gettype($source)
            ));
        }

        return $source;
    }
}
