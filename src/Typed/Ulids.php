<?php

namespace Henzeb\Collection\Typed;

use Henzeb\Collection\Generics\Ulid;
use Henzeb\Collection\Lazy\Ulids as LazyUlids;
use Henzeb\Collection\TypedCollection;

/**
 * @extends TypedCollection<integer|string, string>
 */
class Ulids extends TypedCollection
{
    protected function generics(): string
    {
        return Ulid::class;
    }

    protected function lazyClass(): string
    {
        return LazyUlids::class;
    }
}
