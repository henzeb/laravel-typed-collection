<?php

use Henzeb\Collection\Enums\Type;
use Henzeb\Collection\Exceptions\InvalidTypeException;
use Henzeb\Collection\Lazy\Numerics as LazyNumerics;
use Henzeb\Collection\Typed\Numerics;

it('returns correct generic type', function () {
    $collection = new class extends Numerics {
        public function getGenerics()
        {
            return $this->generics();
        }
    };

    expect($collection->getGenerics())->toBe(Type::Numeric);
});

it('returns correct lazy class', function () {
    $collection = new class extends Numerics {
        public function getLazyClass()
        {
            return $this->lazyClass();
        }
    };

    expect($collection->getLazyClass())->toBe(LazyNumerics::class);
});

it('lazy method returns lazy numerics instance', function () {
    $collection = new Numerics([1, 2.5, '3']);
    $lazy = $collection->lazy();

    expect($lazy)->toBeInstanceOf(LazyNumerics::class);
});

it('accepts numeric values', function () {
    $collection = new Numerics([1, 2.5, '3', '4.7']);

    expect($collection->toArray())->toBe([1, 2.5, '3', '4.7']);
});

it('rejects non-numeric values', function () {
    expect(fn() => new Numerics([1, 'not-numeric']))
        ->toThrow(InvalidTypeException::class);
});