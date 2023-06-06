<?php

namespace Henzeb\Collection\Concerns;

use Henzeb\Collection\Enums\Type;
use Henzeb\Collection\Exceptions\InvalidGenericException;
use Henzeb\Collection\Exceptions\InvalidTypeException;
use Henzeb\Collection\Exceptions\MissingGenericsException;
use Illuminate\Support\Arr;

trait HasGenerics
{

    abstract protected function generics(): string|Type|array;

    /**
     * @throws InvalidGenericException
     * @throws MissingGenericsException
     */
    private function validateGenerics(): void
    {
        $generics = Arr::wrap($this->generics());

        if (empty($generics)) {
            throw new MissingGenericsException;
        }

        foreach ($generics as $generic) {
            if ($generic instanceof Type) {
                continue;
            }

            if (is_object($generic)) {
                throw new InvalidGenericException('object');
            }

            /**
             * @var string $generic
             */

            if (Type::tryFrom($generic)) {
                continue;
            }

            if (class_exists($generic)) {
                continue;
            }

            throw new InvalidGenericException($generic);
        }
    }

    private function validateTypes($items): void
    {
        array_walk(
            $items,
            $this->validateType(...)
        );
    }

    /**
     * @throws InvalidTypeException
     */
    private function validateType(mixed $item): void
    {
        foreach (Arr::wrap($this->generics()) as $generic) {
            if ($this->matchesGeneric($generic, $item)) {
                return;
            }
        }

        throw new InvalidTypeException();
    }

    private function matchesGeneric(mixed $type, mixed $item): bool
    {
        if (is_string($type) && class_exists($type)) {
            return $item instanceof $type;
        }

        if (is_string($type)) {
            $type = Type::tryFrom($type);
        }

        return $type->equals(
            Type::fromValue($item)
        );
    }
}
