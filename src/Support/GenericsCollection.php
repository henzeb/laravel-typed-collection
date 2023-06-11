<?php

namespace Henzeb\Collection\Support;

use Henzeb\Collection\Concerns\CollectsGenerics;
use Henzeb\Collection\Enums\Type;
use Henzeb\Collection\TypedCollection;

class GenericsCollection extends TypedCollection
{
    use CollectsGenerics;

    public function __construct(
        array $items,
        private array|string|Type|null $generics = null
    ) {
        $this->generics = $this->generics ?: $this->collectGenerics();
        parent::__construct($items);
    }

    protected function generics(): string|Type|array
    {
        return $this->generics;
    }

    protected function lazyClass(): string
    {
        return GenericsLazyCollection::class;
    }
}
