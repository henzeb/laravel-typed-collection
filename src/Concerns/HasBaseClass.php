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
    /**
     * @return class-string<BaseCollection<array-key, covariant mixed>>|class-string<LazyCollection<array-key, covariant mixed>>
     */
    protected function baseClass(): string
    {
        return ($this instanceof LazyCollection) ? LazyCollection::class : Collection::class;
    }

    /**
     * Get a base Support collection instance from this collection.
     *
     * @return BaseCollection<array-key, covariant mixed>|LazyCollection<array-key, covariant mixed>
     */
    // @phpstan-ignore method.childReturnType
    public function toBase(): BaseCollection|LazyCollection
    {
        $baseClass = $this->baseClass();

        if ($baseClass === Collection::class) {
            return new BaseCollection($this);
        }

        return new $baseClass($this);
    }
}
