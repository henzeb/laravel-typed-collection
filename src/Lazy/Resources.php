<?php

namespace Henzeb\Collection\Lazy;

use Henzeb\Collection\Enums\Type;
use Henzeb\Collection\LazyTypedCollection;

/**
 * @extends LazyTypedCollection<integer|string, resource>
 */
class Resources extends LazyTypedCollection
{
    protected function generics(): Type
    {
        return Type::Resource;
    }
}
