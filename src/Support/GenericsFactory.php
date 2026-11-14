<?php

namespace Henzeb\Collection\Support;

use Henzeb\Collection\Enums\Type;
use Illuminate\Support\Collection;
use Illuminate\Support\LazyCollection;
use LogicException;

/**
 * @internal
 */
class GenericsFactory
{
    /**
     * @return Collection<array-key, mixed>|LazyCollection<array-key, mixed>
     */
    public function collection(object $collection): Collection|LazyCollection
    {
        if ($collection instanceof Collection || $collection instanceof LazyCollection) {
            return $collection;
        }

        throw new LogicException(
            sprintf('Expected a collection, got %s.', $collection::class)
        );
    }

    /**
     * @param array<array-key, string|Type>|null $generics
     * @param array<array-key, string|Type>|null $keyGenerics
     */
    public function make(?array $generics, ?array $keyGenerics = null): GenericsCollection
    {
        return new GenericsCollection([], $generics, $keyGenerics);
    }
}
