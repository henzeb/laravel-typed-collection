<?php

namespace Henzeb\Collection\Concerns;

use Henzeb\Collection\Enums\Type;

trait HandlesTypeOutput
{
    private function getType(mixed $item): string
    {
        if (is_object($item)) {
            return $item::class;
        }

        return Type::fromValue($item)->value();
    }

    private function typesToString(array $types): string
    {
        return implode(
            ', ',
            array_map(
                function (mixed $type) {
                    if (is_string($type)) {
                        return $type;
                    }

                    if ($type instanceof Type) {
                        return $type->value();
                    }

                    return 'unknown';
                },
                $types
            )
        );
    }
}
