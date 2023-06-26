<?php

namespace Henzeb\Collection\Lazy;

use Henzeb\Collection\Generics\Ulid;
use Henzeb\Collection\LazyTypedCollection;

/**
 * @extends LazyTypedCollection<integer|string, string>
 */
class Ulids extends LazyTypedCollection
{
    protected function generics(): string
    {
        return Ulid::class;
    }
}
