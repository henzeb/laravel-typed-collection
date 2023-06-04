<?php

namespace Henzeb\Collection\Providers;

use Henzeb\Collection\Concerns\CollectsGenerics;
use Henzeb\Collection\Enums\Type;
use Henzeb\Collection\TypedCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;

class TypedCollectionProvider extends ServiceProvider
{
    public function register(): void
    {
        Collection::macro(
            'withGenerics',
            function (string|Type ...$generics) {
                return new class($this->all(), $generics) extends TypedCollection {

                    use CollectsGenerics;

                    public function __construct(
                        array $items,
                        private array|string|Type|null $generics = null
                    ) {
                        $this->generics ??= $this->collectGenerics();
                        parent::__construct($items);
                    }

                    protected function generics(): string|Type|array
                    {
                        return $this->generics;
                    }
                };
            }
        );
    }
}
