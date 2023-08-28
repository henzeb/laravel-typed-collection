<?php

namespace Henzeb\Collection\Contracts;

interface CastableGenericType extends GenericType
{
    public static function castType(mixed $item): mixed;
}
