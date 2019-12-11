<?php

/**
 * Spiral Framework. PHP Data Grid
 *
 * @author Valentin Vintsukevich (vvval)
 */

declare(strict_types=1);

namespace Spiral\DataGrid;

use LogicException;

/**
 * Case insensitive search for a key existence in the given array.
 *
 * @param array  $array
 * @param string $search
 * @return bool
 */
function hasKey(array $array, string $search): bool
{
    foreach ($array as $key => $value) {
        if (strcasecmp($key, $search) === 0) {
            return true;
        }
    }

    return false;
}

/**
 * Get value by a key in the given array using case insensitive case.
 *
 * @param array  $array
 * @param string $search
 * @return mixed
 * @throws LogicException
 */
function getValue(array $array, string $search)
{
    foreach ($array as $key => $value) {
        if (strcasecmp($key, $search) === 0) {
            return $value;
        }
    }

    throw new LogicException("`$search` key is not presented in the array.");
}
