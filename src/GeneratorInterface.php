<?php

/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

declare(strict_types=1);

namespace Spiral\DataGrid;

interface GeneratorInterface
{
    /**
     * Generate new grid view using given source and data schema.
     *
     * @param mixed      $source
     * @param GridSchema $schema
     * @return GridViewInterface
     */
    public function generate($source, GridSchema $schema): GridViewInterface;
}
