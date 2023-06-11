<?php

namespace Henzeb\Collection\Support;

use Henzeb\Collection\Concerns\CollectsGenerics;
use Henzeb\Collection\Enums\Type;
use Henzeb\Collection\LazyTypedCollection;

class GenericsLazyCollection extends LazyTypedCollection
{
    use CollectsGenerics;

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
        return $this->generics;
    }

    protected function keyGenerics(): string|Type|array
    {
        return $this->keyGenerics ?: parent::keyGenerics();
    }
}
