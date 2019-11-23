<?php

/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Valentin Vintsukevich (vvval)
 * @author    Anton Tsitou (Wolfy-J)
 */

declare(strict_types=1);

namespace Spiral\DataGrid\Specification\Filter;

use Spiral\DataGrid\Specification\Value\StringValue;

final class Like extends Expression
{
    /** @var string */
    private $pattern;

    /**
     * @param string $expression
     * @param null   $value
     * @param string $pattern
     */
    public function __construct(string $expression, $value = null, string $pattern = '%%%s%%')
    {
        $this->pattern = $pattern;
        parent::__construct($expression, $value ?? new StringValue());
    }

    /**
     * @return string
     */
    public function getPattern(): string
    {
        return $this->pattern;
    }
}
