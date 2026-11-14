<?php

namespace Henzeb\Collection\Support;

use Henzeb\Collection\Concerns\CollectsGenerics;
use Henzeb\Collection\Enums\Type;
use Henzeb\Collection\TypedCollection;
use Illuminate\Support\LazyCollection;

/**
 * @extends TypedCollection<array-key, mixed>
 */
class GenericsCollection extends TypedCollection
{
    use CollectsGenerics;

    /**
     * @param array<array-key, mixed> $items
     * @param array<array-key, mixed>|string|Type|null $generics
     * @param array<array-key, mixed>|string|Type|null $keyGenerics
     */
    public function __construct(
        array $items,
        private array|string|Type|null $generics = null,
        private array|string|Type|null $keyGenerics = null,
    ) {
        if ($this->keyGenerics) {
            $this->generics = Type::Mixed;
        }

        $this->generics = $this->generics ?: $this->collectGenerics();
        $this->keyGenerics = $this->keyGenerics ?: $this->collectKeyGenerics();
        parent::__construct($items);
    }

    protected function generics(): string|Type|array
    {
        return $this->generics ?: [];
    }

    protected function keyGenerics(): string|Type|array
    {
        return $this->keyGenerics ?: parent::keyGenerics();
    }

    protected function lazyClass(): string
    {
        return GenericsLazyCollection::class;
    }

    public function lazy(): LazyCollection
    {
        return new GenericsLazyCollection(
            $this->all(),
            $this->generics,
            $this->keyGenerics
        );
    }

    /**
     * @return Type|string|array<array-key, mixed>|null
     */
    private function collectGenerics(): Type|string|array|null
    {
        $parent = $this->getParentObject();

        return $parent instanceof TypedCollection ? $parent->generics() : null;
    }

    /**
     * @return Type|string|array<array-key, mixed>|null
     */
    private function collectKeyGenerics(): Type|string|array|null
    {
        $parent = $this->getParentObject();

        return $parent instanceof TypedCollection ? $parent->keyGenerics() : null;
    }
}
