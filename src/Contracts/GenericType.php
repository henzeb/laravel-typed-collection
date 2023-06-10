<?php

namespace Henzeb\Collection\Contracts;

interface GenericType
{
    public static function matchesType(mixed $item): bool;
}
