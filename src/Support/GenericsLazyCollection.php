<?php

namespace Henzeb\Collection\Support;

use Henzeb\Collection\Concerns\CollectsGenerics;
use Henzeb\Collection\Enums\Type;
use Henzeb\Collection\LazyTypedCollection;

/**
 * @extends LazyTypedCollection<array-key, mixed>
 */
class GenericsLazyCollection extends LazyTypedCollection
{
    use CollectsGenerics;

    /**
     * @param iterable<array-key, mixed>|null $items
     * @param array<array-key, mixed>|string|Type|null $generics
     * @param array<array-key, mixed>|string|Type|null $keyGenerics
     */
    public function __construct(
        $items,
        private array|string|Type|null $generics = null,
        private array|string|Type|null $keyGenerics = null
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

    /**
     * @return Type|string|array<array-key, mixed>|null
     */
    private function collectGenerics(): Type|string|array|null
    {
        $parent = $this->getParentObject();

        return $parent instanceof LazyTypedCollection ? $parent->generics() : null;
    }

    /**
     * @return Type|string|array<array-key, mixed>|null
     */
    private function collectKeyGenerics(): Type|string|array|null
    {
        $parent = $this->getParentObject();

        return $parent instanceof LazyTypedCollection ? $parent->keyGenerics() : null;
    }
}
