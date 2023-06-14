<?php

namespace Henzeb\Collection\Typed;

use Henzeb\Collection\Enums\Type;
use Henzeb\Collection\Lazy\Resources as LazyResources;
use Henzeb\Collection\TypedCollection;

/**
 * @extends TypedCollection<integer|string, resource>
 */
class Resources extends TypedCollection
{
    protected function generics(): Type
    {
        return Type::Resource;
    }

    protected function lazyClass(): string
    {
        return LazyResources::class;
    }
}
