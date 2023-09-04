<?php

namespace Henzeb\Collection\Concerns;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as BaseCollection;
use Illuminate\Support\LazyCollection;

/**
 * @internal
 */
trait HasBaseClass
{
    protected function baseClass(): string
    {
        return ($this instanceof LazyCollection) ? LazyCollection::class : Collection::class;
    }

    /**
     * Get a base Support collection instance from this collection.
     *
     * @return BaseCollection<TKey, TValue>|LazyCollection<TKey, TValue>
     */
    public function toBase(): BaseCollection|LazyCollection
    {
        if ($this->baseClass() === Collection::class) {
            return parent::toBase();
        }

        return new ($this->baseClass())($this);
    }
}
