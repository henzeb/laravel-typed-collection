<?php

namespace Henzeb\Collection\Concerns;

use Henzeb\Collection\Contracts\GenericType;
use Henzeb\Collection\Enums\Type;
use Henzeb\Collection\Exceptions\InvalidTypeException;
use Henzeb\Collection\Exceptions\MissingGenericsException;
use Henzeb\Collection\Exceptions\MissingTypedCollectionException;
use Illuminate\Support\Arr;

/**
 * @internal
 */
trait HasGenerics
{

    abstract protected function generics(): string|Type|array;

    /**
     * @throws MissingTypedCollectionException
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
                throw new MissingTypedCollectionException('object');
            }

            /**
             * @var string $generic
             */

            if (Type::tryFrom($generic)) {
                continue;
            }

            if (class_exists($generic) || interface_exists($generic)) {
                continue;
            }

            throw new MissingTypedCollectionException($generic);
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
        $generics = Arr::wrap($this->generics());
        foreach ($generics as $generic) {
            if ($this->matchesGeneric($generic, $item)) {
                return;
            }
        }

        throw new InvalidTypeException(
            self::class,
            $item,
            $generics
        );
    }

    private function matchesGeneric(mixed $type, mixed $item): bool
    {
        if (is_string($type)
            && (class_exists($type) && is_a($type, GenericType::class, true))
        ) {
            return $type::matchesType($item);
        }

        if (is_string($type)
            && (class_exists($type) || interface_exists($type))
        ) {
            return $item instanceof $type;
        }

        if (is_string($type)) {
            $type = Type::tryFrom($type);
        }

        return $type->equals(
                Type::fromValue($item),
            ) || $type->equals(Type::Mixed);
    }

    public function accepts(mixed $item): bool
    {
        try {
            $this->validateType($item);
        } catch (InvalidTypeException) {
            return false;
        }
        return true;
    }
}
