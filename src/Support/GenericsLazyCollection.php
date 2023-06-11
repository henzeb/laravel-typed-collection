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
        private array|string|Type|null $generics = null
    ) {
        $this->generics = $this->generics ?: $this->collectGenerics();
        parent::__construct($items);
    }

    protected function generics(): string|Type|array
    {
        return $this->generics;
    }
}
