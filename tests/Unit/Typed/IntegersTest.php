<?php

use Henzeb\Collection\Enums\Type;
use Henzeb\Collection\Exceptions\InvalidTypeException;
use Henzeb\Collection\Lazy\Integers as LazyIntegers;
use Henzeb\Collection\Typed\Integers;

it('returns correct generic type', function () {
    $collection = new class extends Integers {
        public function getGenerics()
        {
            return $this->generics();
        }
    };

    expect($collection->getGenerics())->toBe(Type::Int);
});

it('returns correct lazy class', function () {
    $collection = new class extends Integers {
        public function getLazyClass()
        {
            return $this->lazyClass();
        }
    };

    expect($collection->getLazyClass())->toBe(LazyIntegers::class);
});

it('lazy method returns lazy integers instance', function () {
    $collection = new Integers([1, 2, 3]);
    $lazy = $collection->lazy();

    expect($lazy)->toBeInstanceOf(LazyIntegers::class);
});

it('accepts integer values', function () {
    $collection = new Integers([1, 2, 3]);

    expect($collection->toArray())->toBe([1, 2, 3]);
});

it('rejects non-integer values', function () {
    expect(fn() => new Integers([1, 'string']))
        ->toThrow(InvalidTypeException::class);
});