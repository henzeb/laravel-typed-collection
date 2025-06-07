<?php

use Henzeb\Collection\Enums\Type;
use Henzeb\Collection\Exceptions\InvalidTypeException;
use Henzeb\Collection\Lazy\Resources as LazyResources;
use Henzeb\Collection\Typed\Resources;

it('returns correct generic type', function () {
    $collection = new class extends Resources {
        public function getGenerics()
        {
            return $this->generics();
        }
    };

    expect($collection->getGenerics())->toBe(Type::Resource);
});

it('returns correct lazy class', function () {
    $collection = new class extends Resources {
        public function getLazyClass()
        {
            return $this->lazyClass();
        }
    };

    expect($collection->getLazyClass())->toBe(LazyResources::class);
});

it('lazy method returns lazy resources instance', function () {
    $resource1 = fopen('php://memory', 'r');
    $resource2 = fopen('php://memory', 'r');
    $collection = new Resources([$resource1, $resource2]);
    $lazy = $collection->lazy();

    expect($lazy)->toBeInstanceOf(LazyResources::class);
    
    fclose($resource1);
    fclose($resource2);
});

it('accepts resource values', function () {
    $resource1 = fopen('php://memory', 'r');
    $resource2 = fopen('php://memory', 'r');
    $collection = new Resources([$resource1, $resource2]);

    expect($collection->toArray())->toBe([$resource1, $resource2]);
    
    fclose($resource1);
    fclose($resource2);
});

it('rejects non-resource values', function () {
    $resource = fopen('php://memory', 'r');
    
    expect(fn() => new Resources([$resource, 'string']))
        ->toThrow(InvalidTypeException::class);
        
    fclose($resource);
});