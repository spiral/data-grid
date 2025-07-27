<?php

declare(strict_types=1);

namespace Spiral\DataGrid\Specification\Value\Accessor;

use Spiral\DataGrid\Specification\ValueInterface;

/**
 * Abstract base class for value accessors that transform input before passing to underlying ValueInterface.
 * Accessors act as middleware for value processing, allowing you to apply transformations like trimming,
 * case conversion, splitting, or other modifications before the actual value validation occurs.
 */
abstract class Accessor implements ValueInterface
{
    public function __construct(
        protected ValueInterface $next,
    ) {}

    final public function accepts(mixed $value): bool
    {
        return $this->acceptsCurrent($value) || $this->next->accepts($value);
    }

    final public function convert(mixed $value): mixed
    {
        return $this->next->convert($this->convertCurrent($value));
    }

    abstract protected function acceptsCurrent(mixed $value): bool;

    abstract protected function convertCurrent(mixed $value): mixed;
}
