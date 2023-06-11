<?php

namespace Henzeb\Collection\Typed;

use Henzeb\Collection\Enums\Type;
use Henzeb\Collection\Lazy\Booleans as LazyBooleans;
use Henzeb\Collection\TypedCollection;

class Booleans extends TypedCollection
{

    protected function generics(): Type
    {
        return Type::Bool;
    }

    protected function lazyClass(): string
    {
        return LazyBooleans::class;
    }
}
