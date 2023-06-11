<?php

namespace Henzeb\Collection\Typed;

use Henzeb\Collection\Enums\Type;
use Henzeb\Collection\Lazy\Integers as LazyIntegers;
use Henzeb\Collection\TypedCollection;

class Integers extends TypedCollection
{

    protected function generics(): Type
    {
        return Type::Int;
    }

    protected function lazyClass(): string
    {
        return LazyIntegers::class;
    }
}
