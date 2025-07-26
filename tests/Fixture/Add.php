<?php

/**
 * Spiral Framework. PHP Data Grid
 *
 * @author Valentin Vintsukevich (vvval)
 */

declare(strict_types=1);

namespace Spiral\Tests\DataGrid\Fixture;

use Spiral\DataGrid\Specification\Value\Accessor\Accessor;
use Spiral\DataGrid\Specification\ValueInterface;

class Add extends Accessor
{
    public function __construct(ValueInterface $next, private readonly int $val)
    {
        parent::__construct($next);
    }

    protected function acceptsCurrent($value): bool
    {
        return \is_numeric($value);
    }

    protected function convertCurrent($value): mixed
    {
        return $value + $this->val;
    }
}
