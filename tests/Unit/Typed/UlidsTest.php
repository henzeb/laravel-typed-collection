<?php

use Henzeb\Collection\Exceptions\InvalidTypeException;
use Henzeb\Collection\Generics\Ulid;
use Henzeb\Collection\Lazy\Ulids as LazyUlids;
use Henzeb\Collection\Typed\Ulids;

it('returns correct generic type', function () {
    $collection = new class extends Ulids {
        public function getGenerics()
        {
            return $this->generics();
        }
    };

    expect($collection->getGenerics())->toBe(Ulid::class);
});

it('returns correct lazy class', function () {
    $collection = new class extends Ulids {
        public function getLazyClass()
        {
            return $this->lazyClass();
        }
    };

    expect($collection->getLazyClass())->toBe(LazyUlids::class);
});

it('lazy method returns lazy ulids instance', function () {
    $ulid1 = '01ARZ3NDEKTSV4RRFFQ69G5FAV';
    $ulid2 = '01BX5ZZKBKACTAV9WEVGEMMVRY';
    $collection = new Ulids([$ulid1, $ulid2]);
    $lazy = $collection->lazy();

    expect($lazy)->toBeInstanceOf(LazyUlids::class);
});

it('accepts valid ulid values', function () {
    $ulid1 = '01ARZ3NDEKTSV4RRFFQ69G5FAV';
    $ulid2 = '01BX5ZZKBKACTAV9WEVGEMMVRY';
    $collection = new Ulids([$ulid1, $ulid2]);

    expect($collection->toArray())->toBe([$ulid1, $ulid2]);
});

it('rejects invalid ulid values', function () {
    $validUlid = '01ARZ3NDEKTSV4RRFFQ69G5FAV';
    expect(fn() => new Ulids([$validUlid, 'invalid-ulid']))
        ->toThrow(InvalidTypeException::class);
});

it('rejects non-string values', function () {
    $validUlid = '01ARZ3NDEKTSV4RRFFQ69G5FAV';
    expect(fn() => new Ulids([$validUlid, 123]))
        ->toThrow(InvalidTypeException::class);
});