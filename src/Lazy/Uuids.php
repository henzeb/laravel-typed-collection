<?php

namespace Henzeb\Collection\Lazy;

use Henzeb\Collection\Generics\Uuid;
use Henzeb\Collection\LazyTypedCollection;

/**
 * @extends LazyTypedCollection<integer|string, string>
 */
class Uuids extends LazyTypedCollection
{
    protected function generics(): string
    {
        return Uuid::class;
    }
}
