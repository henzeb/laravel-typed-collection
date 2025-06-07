<?php

use Henzeb\Collection\Enums\Type;
use Henzeb\Collection\Exceptions\InvalidTypeException;
use Henzeb\Collection\Lazy\Booleans as LazyBooleans;
use Henzeb\Collection\Typed\Booleans;

it('returns correct generic type', function () {
    $collection = new class extends Booleans {
        public function getGenerics()
        {
            return $this->generics();
        }
    };

    expect($collection->getGenerics())->toBe(Type::Bool);
});

it('returns correct lazy class', function () {
    $collection = new class extends Booleans {
        public function getLazyClass()
        {
            return $this->lazyClass();
        }
    };

    expect($collection->getLazyClass())->toBe(LazyBooleans::class);
});

it('lazy method returns lazy booleans instance', function () {
    $collection = new Booleans([true, false]);
    $lazy = $collection->lazy();

    expect($lazy)->toBeInstanceOf(LazyBooleans::class);
});

it('accepts boolean values', function () {
    $collection = new Booleans([true, false, true]);

    expect($collection->toArray())->toBe([true, false, true]);
});

it('rejects non-boolean values', function () {
    expect(fn() => new Booleans([true, 'string']))
        ->toThrow(InvalidTypeException::class);
});