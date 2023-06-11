<?php

namespace Henzeb\Collection\Lazy;

use Henzeb\Collection\Enums\Type;
use Henzeb\Collection\LazyTypedCollection;

class Booleans extends LazyTypedCollection
{
    protected function generics(): Type
    {
        return Type::Bool;
    }
}
