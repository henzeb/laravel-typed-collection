<?php

namespace Henzeb\Collection;

use Henzeb\Collection\Concerns\HasGenericKeys;
use Henzeb\Collection\Concerns\HasGenerics;
use Henzeb\Collection\Concerns\OverridesMethods;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as BaseCollection;

/**
 * @template TKey of array-key
 *
 * @template-covariant TValue
 *
 * @extends Collection<TKey, TValue>
 */
abstract class EloquentTypedCollection extends Collection
{
    use HasGenerics, HasGenericKeys, OverridesMethods;

    protected function baseClass(): string
    {
        return Collection::class;
    }

    /**
     * Get a base Support collection instance from this collection.
     *
     * @return BaseCollection<TKey, TValue>
     */
    public function toBase(): BaseCollection
    {
        if ($this->baseClass() === Collection::class) {
            return parent::toBase();
        }

        return new ($this->baseClass())($this);
    }
}
