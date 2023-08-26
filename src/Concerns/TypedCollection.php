<?php

namespace Henzeb\Collection\Concerns;

use Henzeb\Collection\EloquentTypedCollection;
use Henzeb\Collection\Exceptions\InvalidTypedCollectionException;
use Henzeb\Collection\Exceptions\MissingTypedCollectionException;
use Henzeb\Collection\LazyTypedCollection;
use Henzeb\Collection\TypedCollection as TypedCollectionClass;

/**
 * @property string $typedCollection
 */
trait TypedCollection
{
    private function isValidTypedCollection(): bool
    {
        return is_a($this->typedCollection, TypedCollectionClass::class, true)
            || is_a($this->typedCollection, EloquentTypedCollection::class, true)
            || is_a($this->typedCollection, LazyTypedCollection::class, true);
    }

    /**
     * @param array $models
     * @return TypedCollectionClass|EloquentTypedCollection|LazyTypedCollection
     * @throws InvalidTypedCollectionException
     * @throws MissingTypedCollectionException
     */
    public function newCollection(array $models = []): TypedCollectionClass|EloquentTypedCollection|LazyTypedCollection
    {
        if (isset($this->typedCollection)) {
            if ($this->isValidTypedCollection()) {
                return new ($this->typedCollection)($models);
            }

            throw new InvalidTypedCollectionException(self::class);
        }

        throw new MissingTypedCollectionException(self::class);
    }
}
