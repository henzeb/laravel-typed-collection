<?php

namespace Henzeb\Collection\Typed;

use Henzeb\Collection\Enums\Type;
use Henzeb\Collection\Lazy\Numerics as LazyNumerics;
use Henzeb\Collection\TypedCollection;

/**
 * @extends TypedCollection<integer|string, numeric>
 */
class Numerics extends TypedCollection
{
    protected function generics(): Type
    {
        return Type::Numeric;
    }

    protected function lazyClass(): string
    {
        return LazyNumerics::class;
    }
}
