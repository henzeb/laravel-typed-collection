<?php

namespace Henzeb\Collection\Lazy;

use Henzeb\Collection\Enums\Type;
use Henzeb\Collection\LazyTypedCollection;

/**
 * @extends LazyTypedCollection<integer|string, string>
 */
class Strings extends LazyTypedCollection
{
    protected function generics(): Type
    {
        return Type::String;
    }
}
