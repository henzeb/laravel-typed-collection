<?php

namespace Henzeb\Collection\Lazy;

use Henzeb\Collection\Enums\Type;
use Henzeb\Collection\LazyTypedCollection;

/**
 * @extends LazyTypedCollection<integer|string, integer>
 */
class Integers extends LazyTypedCollection
{
    protected function generics(): Type
    {
        return Type::Int;
    }
}
