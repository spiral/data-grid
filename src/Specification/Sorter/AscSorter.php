<?php

/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Valentin Vintsukevich (vvval), Anton Titov (Wolfy-J)
 */

declare(strict_types=1);

namespace Spiral\DataGrid\Specification\Sorter;

use Spiral\DataGrid\Specification\SorterInterface;
use Spiral\DataGrid\SpecificationInterface;

final class AscSorter implements SorterInterface
{
    /** @var array */
    private $expressions;

    /**
     * AscSorter constructor.
     *
     * @param string ...$expressions
     */
    public function __construct(string ...$expressions)
    {
        $this->expressions = $expressions;
    }

    /**
     * @inheritDoc
     */
    public function withDirection($direction): ?SpecificationInterface
    {
        return $this;
    }

    /**
     * @return array
     */
    public function getExpressions(): array
    {
        $expression = [];
        foreach ($this->expressions as $name) {
            $expression[$name] = self::ASC;
        }

        return $expression;
    }

    /**
     * @inheritDoc
     */
    public function getValue(): string
    {
        return self::ASC;
    }
}
