<?php

namespace Henzeb\Collection\Mixins;

use Closure;
use Henzeb\Collection\Enums\Type;
use Henzeb\Collection\Support\GenericsCollection;
use Henzeb\Collection\Support\GenericsLazyCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\LazyCollection;

class CollectionMixin
{
    public function withGenerics(): Closure
    {
        return function (string|Type ...$generics): GenericsLazyCollection|GenericsCollection {
            /**
             * @var $this Collection
             */
            if ($this instanceof LazyCollection) {
                return new GenericsLazyCollection(
                    $this,
                    $generics
                );
            }

            return new GenericsCollection(
                $this->all(),
                $generics
            );
        };
    }

    public function withKeyGenerics(): Closure
    {
        return function (string|Type ...$generics): GenericsLazyCollection|GenericsCollection {
            /**
             * @var $this Collection
             */
            if ($this instanceof LazyCollection) {
                return new GenericsLazyCollection(
                    $this,
                    null,
                    $generics
                );
            }

            return new GenericsCollection(
                $this->all(),
                null,
                $generics
            );
        };
    }

    public function onlyGenerics(): Closure
    {
        return function (string|Type ...$generics) {
            /**
             * @var $this Collection|LazyCollection
             */
            $collection = collect()->withGenerics(...$generics);
            return $this->filter(
                function (mixed $item) use ($collection) {
                    return $collection->accepts($item);
                }
            );
        };
    }

    public function onlyKeyGenerics(): Closure
    {
        return function (string|Type ...$generics) {
            /**
             * @var $this Collection|LazyCollection
             */
            $collection = collect()->withKeyGenerics(...$generics);
            return $this->filter(
                function (mixed $item) use ($collection) {
                    return $collection->acceptsKey($item);
                }
            );
        };
    }
}
