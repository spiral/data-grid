<?php

/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Valentin Vintsukevich (vvval)
 * @author    Anton Tsitou (Wolfy-J)
 */

declare(strict_types=1);

namespace Spiral\DataGrid\Specification\Sorter;

final class DescSorter extends AbstractSorter
{
    /**
     * @inheritDoc
     */
    public function getValue(): string
    {
        return self::DESC;
    }
}
