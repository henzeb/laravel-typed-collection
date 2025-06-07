<?php

use Henzeb\Collection\Enums\Type;
use Henzeb\Collection\Exceptions\InvalidTypeException;
use Henzeb\Collection\Lazy\Arrays as LazyArrays;
use Henzeb\Collection\Typed\Arrays;

it('returns correct generic type', function () {
    $collection = new class extends Arrays {
        public function getGenerics()
        {
            return $this->generics();
        }
    };

    expect($collection->getGenerics())->toBe(Type::Array);
});

it('returns correct lazy class', function () {
    $collection = new class extends Arrays {
        public function getLazyClass()
        {
            return $this->lazyClass();
        }
    };

    expect($collection->getLazyClass())->toBe(LazyArrays::class);
});

it('lazy method returns lazy arrays instance', function () {
    $collection = new Arrays([['a'], ['b']]);
    $lazy = $collection->lazy();

    expect($lazy)->toBeInstanceOf(LazyArrays::class);
});

it('accepts array values', function () {
    $collection = new Arrays([['a'], ['b', 'c']]);

    expect($collection->toArray())->toBe([['a'], ['b', 'c']]);
});

it('rejects non-array values', function () {
    expect(fn() => new Arrays(['string', 123]))
        ->toThrow(InvalidTypeException::class);
});