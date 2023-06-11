<?php

namespace Henzeb\Collection\Typed;

use Henzeb\Collection\Enums\Type;
use Henzeb\Collection\Generics\Uuid;
use Henzeb\Collection\Lazy\Uuids as LazyUuids;
use Henzeb\Collection\TypedCollection;

class Uuids extends TypedCollection
{
    protected function generics(): string|Type|array
    {
        return Uuid::class;
    }

    protected function lazyClass(): string
    {
        return LazyUuids::class;
    }
}
