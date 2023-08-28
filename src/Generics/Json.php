<?php

namespace Henzeb\Collection\Generics;

use Henzeb\Collection\Contracts\CastableGenericType;
use Henzeb\Collection\Contracts\GenericType;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Support\Arr;
use JsonSerializable;
use Throwable;
use Traversable;

class Json implements GenericType, CastableGenericType
{
    private static ?bool $useJsonValidate = null;

    public static function castType(mixed $item): mixed
    {
        $item = match (true) {
            $item instanceof Jsonable => $item->toJson(),
            $item instanceof Arrayable => $item->toArray(),
            $item instanceof Traversable => iterator_to_array($item),
            $item instanceof JsonSerializable => Arr::wrap($item->jsonSerialize()),
            default => $item,
        };

        if (is_array($item)) {
            return json_encode($item);
        }

        return $item;
    }

    public static function matchesType(mixed $item): bool
    {
        return is_string($item) && self::validateJson($item);
    }

    private static function validateJson(mixed $item): bool
    {
        if (self::$useJsonValidate ??= function_exists('json_validate')) {
            return json_validate($item);
        }

        try {
            json_decode(
                $item,
                true,
                512,
                JSON_THROW_ON_ERROR
            );
        } catch (Throwable) {
            return false;
        }

        return true;
    }
}
