<?php

namespace Henzeb\Collection\Mixins;

use Closure;
use Henzeb\Collection\Enums\Type;
use Henzeb\Collection\Support\GenericsCollection;
use Henzeb\Collection\Support\GenericsFactory;
use Henzeb\Collection\Support\GenericsLazyCollection;
use Illuminate\Support\LazyCollection;

class CollectionMixin
{
    public function withGenerics(): Closure
    {
        $factory = new GenericsFactory();

        return function (string|Type ...$generics) use ($factory): GenericsLazyCollection|GenericsCollection {
            $collection = $factory->collection($this);

            if ($collection instanceof LazyCollection) {
                return new GenericsLazyCollection(
                    $collection,
                    $generics
                );
            }

            return new GenericsCollection(
                $collection->all(),
                $generics
            );
        };
    }

    public function withKeyGenerics(): Closure
    {
        $factory = new GenericsFactory();

        return function (string|Type ...$generics) use ($factory): GenericsLazyCollection|GenericsCollection {
            $collection = $factory->collection($this);

            if ($collection instanceof LazyCollection) {
                return new GenericsLazyCollection(
                    $collection,
                    null,
                    $generics
                );
            }

            return new GenericsCollection(
                $collection->all(),
                null,
                $generics
            );
        };
    }

    public function onlyGenerics(): Closure
    {
        $factory = new GenericsFactory();

        return function (string|Type ...$generics) use ($factory) {
            $collection = $factory->make($generics);

            return $factory->collection($this)->filter(
                function (mixed $item) use ($collection) {
                    return $collection->accepts($item);
                }
            );
        };
    }

    public function onlyKeyGenerics(): Closure
    {
        $factory = new GenericsFactory();

        return function (string|Type ...$generics) use ($factory) {
            $collection = $factory->make(null, $generics);

            return $factory->collection($this)->filter(
                function (mixed $item) use ($collection) {
                    return $collection->acceptsKey($item);
                }
            );
        };
    }
}
