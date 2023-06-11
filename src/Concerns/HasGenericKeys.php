<?php

namespace Henzeb\Collection\Concerns;

use Henzeb\Collection\Contracts\GenericType;
use Henzeb\Collection\Enums\Type;
use Henzeb\Collection\Exceptions\InvalidKeyGenericException;
use Henzeb\Collection\Exceptions\InvalidKeyTypeException;
use Henzeb\Collection\Exceptions\MissingKeyGenericsException;
use Illuminate\Support\Arr;

trait HasGenericKeys
{
    protected function keyGenerics(): string|Type|array
    {
        return Type::keyables();
    }

    private function validateKeyGenerics(): void
    {
        $generics = Arr::wrap($this->keyGenerics());

        if ($generics === Type::keyables()) {
            return;
        }

        if (empty($generics)) {
            throw new MissingKeyGenericsException();
        }

        foreach ($generics as $generic) {
            if (is_string($generic)) {
                $generic = Type::tryFrom($generic) ?: $generic;
            }

            if ($generic instanceof Type) {
                if ($generic->keyable()) {
                    continue;
                }
            }

            if (is_string($generic) && is_a($generic, GenericType::class, true)) {
                continue;
            }

            throw new InvalidKeyGenericException(
                $generic
            );
        }
    }

    private function validateKeyTypes(array $items)
    {
        if ($this->keyGenerics() === Type::keyables()) {
            return;
        }

        foreach (array_keys($items) as $key) {
            $this->validateKeyType($key);
        }
    }

    private function validateKeyType(mixed $item): void
    {
        $generics = Arr::wrap($this->keyGenerics());

        foreach ($generics as $generic) {
            if ($this->matchesKeyGeneric($generic, $item)) {
                return;
            }
        }

        throw new InvalidKeyTypeException(
            self::class,
            $item,
            $generics
        );
    }

    private function matchesKeyGeneric(string|Type $generic, mixed $key): bool
    {
        $keyType = Type::fromValue($key);

        if (!$keyType->keyable()) {
            return false;
        }

        if ($this->keyGenerics() === Type::keyables()) {
            return true;
        }

        if (is_a($generic, GenericType::class, true)
        ) {
            return $generic::matchesType($key);
        }

        if (is_string($generic)) {
            $generic = Type::tryFrom($generic);
        }

        return $generic->equals($keyType);
    }

    public function acceptsKey(mixed $item): bool
    {
        try {
            $this->validateKeyType($item);
        } catch (InvalidKeyTypeException) {
            return false;
        }
        return true;
    }
}
