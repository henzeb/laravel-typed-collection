<?php

use Henzeb\Collection\Enums\Type;
use Henzeb\Collection\Exceptions\InvalidTypeException;
use Henzeb\Collection\Lazy\Strings as LazyStrings;
use Henzeb\Collection\Typed\Strings;

it('returns correct generic type', function () {
    $collection = new class extends Strings {
        public function getGenerics()
        {
            return $this->generics();
        }
    };

    expect($collection->getGenerics())->toBe(Type::String);
});

it('returns correct lazy class', function () {
    $collection = new class extends Strings {
        public function getLazyClass()
        {
            return $this->lazyClass();
        }
    };

    expect($collection->getLazyClass())->toBe(LazyStrings::class);
});

it('lazy method returns lazy strings instance', function () {
    $collection = new Strings(['hello', 'world']);
    $lazy = $collection->lazy();

    expect($lazy)->toBeInstanceOf(LazyStrings::class);
});

it('accepts string values', function () {
    $collection = new Strings(['hello', 'world']);

    expect($collection->toArray())->toBe(['hello', 'world']);
});

it('rejects non-string values', function () {
    expect(fn() => new Strings(['string', 123]))
        ->toThrow(InvalidTypeException::class);
});