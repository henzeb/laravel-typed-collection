<?php

use Henzeb\Collection\Exceptions\InvalidTypeException;
use Henzeb\Collection\Generics\Json;
use Henzeb\Collection\Lazy\Jsons as LazyJsons;
use Henzeb\Collection\Typed\Jsons;

it('returns correct generic type', function () {
    $collection = new class extends Jsons {
        public function getGenerics()
        {
            return $this->generics();
        }
    };

    expect($collection->getGenerics())->toBe(Json::class);
});

it('returns correct lazy class', function () {
    $collection = new class extends Jsons {
        public function getLazyClass()
        {
            return $this->lazyClass();
        }
    };

    expect($collection->getLazyClass())->toBe(LazyJsons::class);
});

it('lazy method returns lazy jsons instance', function () {
    $collection = new Jsons(['{"key": "value"}', '{"foo": "bar"}']);
    $lazy = $collection->lazy();

    expect($lazy)->toBeInstanceOf(LazyJsons::class);
});

it('accepts valid json values', function () {
    $json1 = '{"key": "value"}';
    $json2 = '{"foo": "bar"}';
    $collection = new Jsons([$json1, $json2]);

    expect($collection->toArray())->toBe([$json1, $json2]);
});

it('rejects invalid json values', function () {
    expect(fn() => new Jsons(['{"valid": "json"}', 'invalid-json']))
        ->toThrow(InvalidTypeException::class);
});

it('rejects non-string values', function () {
    expect(fn() => new Jsons(['{"valid": "json"}', 123]))
        ->toThrow(InvalidTypeException::class);
});