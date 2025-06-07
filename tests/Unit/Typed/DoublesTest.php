<?php

use Henzeb\Collection\Enums\Type;
use Henzeb\Collection\Exceptions\InvalidTypeException;
use Henzeb\Collection\Lazy\Doubles as LazyDoubles;
use Henzeb\Collection\Typed\Doubles;

it('returns correct generic type', function () {
    $collection = new class extends Doubles {
        public function getGenerics()
        {
            return $this->generics();
        }
    };

    expect($collection->getGenerics())->toBe(Type::Double);
});

it('returns correct lazy class', function () {
    $collection = new class extends Doubles {
        public function getLazyClass()
        {
            return $this->lazyClass();
        }
    };

    expect($collection->getLazyClass())->toBe(LazyDoubles::class);
});

it('lazy method returns lazy doubles instance', function () {
    $collection = new Doubles([1.5, 2.7, 3.14]);
    $lazy = $collection->lazy();

    expect($lazy)->toBeInstanceOf(LazyDoubles::class);
});

it('accepts double values', function () {
    $collection = new Doubles([1.5, 2.7, 3.14]);

    expect($collection->toArray())->toBe([1.5, 2.7, 3.14]);
});

it('rejects non-double values', function () {
    expect(fn() => new Doubles([1.5, 'string']))
        ->toThrow(InvalidTypeException::class);
});