<?php

namespace Henzeb\Collection\Lazy;

use Henzeb\Collection\Enums\Type;
use Henzeb\Collection\LazyTypedCollection;

/**
 * @extends LazyTypedCollection<integer|string, numeric>
 */
class Numerics extends LazyTypedCollection
{
    protected function generics(): Type
    {
        return Type::Numeric;
    }
}
