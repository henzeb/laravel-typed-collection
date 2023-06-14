<?php

namespace Henzeb\Collection\Typed;

use Henzeb\Collection\Enums\Type;
use Henzeb\Collection\Lazy\Strings as LazyStrings;
use Henzeb\Collection\TypedCollection;

/**
 * @extends TypedCollection<integer|string, string>
 */
class Strings extends TypedCollection
{
    protected function generics(): Type
    {
        return Type::String;
    }

    protected function lazyClass(): string
    {
        return LazyStrings::class;
    }
}
