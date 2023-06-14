<?php

namespace Henzeb\Collection\Typed;

use Henzeb\Collection\Enums\Type;
use Henzeb\Collection\Lazy\Doubles as LazyDoubles;
use Henzeb\Collection\TypedCollection;

/**
 * @extends TypedCollection<integer|string, float>
 */
class Doubles extends TypedCollection
{
    protected function generics(): Type
    {
        return Type::Double;
    }

    protected function lazyClass(): string
    {
        return LazyDoubles::class;
    }
}
