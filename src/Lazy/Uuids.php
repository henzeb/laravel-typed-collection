<?php

namespace Henzeb\Collection\Lazy;

use Henzeb\Collection\Generics\Uuid;
use Henzeb\Collection\LazyTypedCollection;

class Uuids extends LazyTypedCollection
{
    protected function generics(): string
    {
        return Uuid::class;
    }
}
