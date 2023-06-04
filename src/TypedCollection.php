<?php

namespace Henzeb\Collection;

use Henzeb\Collection\Enums\Type;
use Henzeb\Collection\Exceptions\InvalidGenericException;
use Henzeb\Collection\Exceptions\InvalidTypeException;
use Henzeb\Collection\Exceptions\MissingGenericsException;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

abstract class TypedCollection extends Collection
{
    public function __construct(
        $items = [],
    ) {
        $this->validateGenerics();

        $items = $this->getArrayableItems($items);

        $this->validateTypes($items);

        parent::__construct(
            $items
        );
    }

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

    public function offsetSet($key, $value): void
    {
        $this->validateType($value);

        parent::offsetSet($key, $value);
    }

    public function add($item): static
    {
        return $this->put(null, $item);
    }

    public function push(...$values): static
    {
        $this->validateTypes($values);

        return parent::push(...$values);
    }

    public function prepend($value, $key = null): static
    {
        $this->validateType($value);

        return parent::prepend(...func_get_args());
    }
}
