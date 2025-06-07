<?php

use Henzeb\Collection\Exceptions\InvalidTypeException;
use Henzeb\Collection\Generics\Uuid;
use Henzeb\Collection\Lazy\Uuids as LazyUuids;
use Henzeb\Collection\Typed\Uuids;

it('returns correct generic type', function () {
    $collection = new class extends Uuids {
        public function getGenerics()
        {
            return $this->generics();
        }
    };

    expect($collection->getGenerics())->toBe(Uuid::class);
});

it('returns correct lazy class', function () {
    $collection = new class extends Uuids {
        public function getLazyClass()
        {
            return $this->lazyClass();
        }
    };

    expect($collection->getLazyClass())->toBe(LazyUuids::class);
});

it('lazy method returns lazy uuids instance', function () {
    $uuid1 = '550e8400-e29b-41d4-a716-446655440000';
    $uuid2 = '6ba7b810-9dad-11d1-80b4-00c04fd430c8';
    $collection = new Uuids([$uuid1, $uuid2]);
    $lazy = $collection->lazy();

    expect($lazy)->toBeInstanceOf(LazyUuids::class);
});

it('accepts valid uuid values', function () {
    $uuid1 = '550e8400-e29b-41d4-a716-446655440000';
    $uuid2 = '6ba7b810-9dad-11d1-80b4-00c04fd430c8';
    $collection = new Uuids([$uuid1, $uuid2]);

    expect($collection->toArray())->toBe([$uuid1, $uuid2]);
});

it('rejects invalid uuid values', function () {
    $validUuid = '550e8400-e29b-41d4-a716-446655440000';
    expect(fn() => new Uuids([$validUuid, 'invalid-uuid']))
        ->toThrow(InvalidTypeException::class);
});

it('rejects non-string values', function () {
    $validUuid = '550e8400-e29b-41d4-a716-446655440000';
    expect(fn() => new Uuids([$validUuid, 123]))
        ->toThrow(InvalidTypeException::class);
});