<?php

namespace Henzeb\Collection\Generics;

use Henzeb\Collection\Contracts\GenericType;
use Ramsey\Uuid\Uuid as RamseyUuid;

class Uuid implements GenericType
{
    public static function matchesType(mixed $item): bool
    {
        return is_string($item) && RamseyUuid::isValid($item);
    }
}
