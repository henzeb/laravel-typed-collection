<?php

namespace Henzeb\Collection\Typed;

use Henzeb\Collection\Enums\Type;
use Henzeb\Collection\Lazy\Arrays as LazyArrays;
use Henzeb\Collection\TypedCollection;

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
