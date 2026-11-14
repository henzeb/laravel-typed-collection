<?php

use Henzeb\Collection\Support\GenericsCollection;
use Henzeb\Collection\Support\GenericsFactory;
use Illuminate\Support\Collection;
use Illuminate\Support\LazyCollection;

test('collection returns collections', function (object $collection) {
    expect((new GenericsFactory())->collection($collection))->toBe($collection);
})->with([
    'collection' => [new Collection()],
    'lazy collection' => [new LazyCollection()],
]);

test('collection rejects other objects', function () {
    expect(fn () => (new GenericsFactory())->collection(new stdClass()))
        ->toThrow(LogicException::class, 'Expected a collection, got stdClass.');
});

test('make creates generics collection', function () {
    $collection = (new GenericsFactory())->make(['string']);

    expect($collection)->toBeInstanceOf(GenericsCollection::class)
        ->and($collection->accepts('hello'))->toBeTrue()
        ->and($collection->accepts(1))->toBeFalse();
});

test('make creates key generics collection', function () {
    $collection = (new GenericsFactory())->make(null, ['int']);

    expect($collection->acceptsKey(1))->toBeTrue()
        ->and($collection->acceptsKey('hello'))->toBeFalse();
});
