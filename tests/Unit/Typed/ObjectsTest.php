<?php

use Henzeb\Collection\Enums\Type;
use Henzeb\Collection\Exceptions\InvalidTypeException;
use Henzeb\Collection\Lazy\Objects as LazyObjects;
use Henzeb\Collection\Typed\Objects;

it('returns correct generic type', function () {
    $collection = new class extends Objects {
        public function getGenerics()
        {
            return $this->generics();
        }
    };

    expect($collection->getGenerics())->toBe(Type::Object);
});

it('returns correct lazy class', function () {
    $collection = new class extends Objects {
        public function getLazyClass()
        {
            return $this->lazyClass();
        }
    };

    expect($collection->getLazyClass())->toBe(LazyObjects::class);
});

it('lazy method returns lazy objects instance', function () {
    $obj1 = new stdClass();
    $obj2 = new stdClass();
    $collection = new Objects([$obj1, $obj2]);
    $lazy = $collection->lazy();

    expect($lazy)->toBeInstanceOf(LazyObjects::class);
});

it('accepts object values', function () {
    $obj1 = new stdClass();
    $obj2 = new stdClass();
    $collection = new Objects([$obj1, $obj2]);

    expect($collection->toArray())->toBe([$obj1, $obj2]);
});

it('rejects non-object values', function () {
    $obj = new stdClass();
    expect(fn() => new Objects([$obj, 'string']))
        ->toThrow(InvalidTypeException::class);
});