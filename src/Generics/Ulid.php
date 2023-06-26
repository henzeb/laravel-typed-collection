<?php

namespace Henzeb\Collection\Generics;

use Henzeb\Collection\Contracts\GenericType;
use Symfony\Component\Uid\Ulid as SymfonyUlid;

class Ulid implements GenericType
{
    public static function matchesType(mixed $item): bool
    {
        return is_string($item) && SymfonyUlid::isValid($item);
    }
}
