<?php

namespace Henzeb\Collection\Typed;

use Henzeb\Collection\Enums\Type;
use Henzeb\Collection\Lazy\Arrays as LazyArrays;
use Henzeb\Collection\TypedCollection;

/**
 * @extends TypedCollection<integer|string, array>
 */
class Arrays extends TypedCollection
{
    protected function generics(): Type
    {
        return Type::Array;
    }

    protected function lazyClass(): string
    {
        return LazyArrays::class;
    }
}
