<?php

namespace Henzeb\Collection\Typed;

use Henzeb\Collection\Enums\Type;
use Henzeb\Collection\Lazy\Objects as LazyObjects;
use Henzeb\Collection\TypedCollection;

/**
 * @extends TypedCollection<integer|string, object>
 */
class Objects extends TypedCollection
{
    protected function generics(): Type
    {
        return Type::Object;
    }

    protected function lazyClass(): string
    {
        return LazyObjects::class;
    }
}
