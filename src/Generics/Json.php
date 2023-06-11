<?php

namespace Henzeb\Collection\Generics;

use Henzeb\Collection\Contracts\GenericType;
use Throwable;

class Json implements GenericType
{
    private static ?bool $native = null;

    public static function matchesType(mixed $item): bool
    {
        return is_string($item) && self::validateJson($item);
    }

    private static function validateJson(mixed $item): bool
    {
        if (self::$native ??= function_exists('json_validate')) {
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
