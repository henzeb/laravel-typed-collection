<?php

namespace Henzeb\Collection\Typed;

use Henzeb\Collection\Generics\Ulid;
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
}
