<?php

namespace Henzeb\Collection\Exceptions;

use Exception;
use Henzeb\Collection\Enums\Type;

class InvalidTypeException extends Exception
{
    public function __construct(string $class, mixed $item, array $types)
    {
        parent::__construct(
            sprintf(
                '%s: The given value `%s` does not match (one of) the generic type [%s] for this collection.',
                $class,
                $this->getType($item),
                $this->typesToString($types),
            )
        );
    }

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
