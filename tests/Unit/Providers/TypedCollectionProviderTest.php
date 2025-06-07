<?php

use Henzeb\Collection\Enums\Type;
use Henzeb\Collection\Exceptions\InvalidKeyTypeException;
use Henzeb\Collection\Exceptions\InvalidTypeException;
use Henzeb\Collection\Exceptions\MissingGenericsException;
use Henzeb\Collection\Generics\Uuid;
use Henzeb\Collection\LazyTypedCollection;
use Henzeb\Collection\Providers\TypedCollectionProvider;
use Henzeb\Collection\TypedCollection;

beforeEach(function () {
    $this->app->register(TypedCollectionProvider::class);
});

test('get with generics', function () {
    $collection = collect('initial value')->withGenerics('string');

    expect($collection)->toBeInstanceOf(TypedCollection::class);

    $collection->add('a string');

    $collection[] = 'another string';

    expect($collection->all())->toBe([
        'initial value',
        'a string',
        'another string',
    ]);

    expect(fn() => $collection->put('test', $this))
        ->toThrow(InvalidTypeException::class);
});

test('get with generics without types', function () {
    expect(fn() => collect()->withGenerics())
        ->toThrow(MissingGenericsException::class);
});

test('get with generics without types lazy', function () {
    expect(fn() => collect()->lazy()->withGenerics())
        ->toThrow(MissingGenericsException::class);
});

test('get with generics lazy', function () {
    $collection = collect(['hello world'])
        ->lazy()
        ->withGenerics('string');

    expect($collection)->toBeInstanceOf(LazyTypedCollection::class);

    expect($collection->all())->toBe([
        'hello world',
    ]);

    $collection = collect(['hello world', true])
        ->lazy()
        ->withGenerics('string')
        ->getIterator();

    expect(fn() => $collection->next())
        ->toThrow(InvalidTypeException::class);
});

test('typed collection with generics', function () {
    $typed = new class(['initial value']) extends TypedCollection {
        protected function generics(): string|Type|array
        {
            return Type::String;
        }
    };

    $collection = $typed->withGenerics();

    expect($collection)->toBeInstanceOf(TypedCollection::class);

    $collection->add('a string');

    $collection[] = 'another string';

    expect($collection->all())->toBe([
        'initial value',
        'a string',
        'another string',
    ]);

    expect(fn() => $collection->put('test', $this))
        ->toThrow(InvalidTypeException::class);
});

test('typed collection with key generics', function () {
    $uuid1 = \Ramsey\Uuid\Uuid::uuid4()->toString();
    $uuid2 = \Ramsey\Uuid\Uuid::uuid4()->toString();
    $uuid3 = \Ramsey\Uuid\Uuid::uuid4()->toString();

    $typed = new class([$uuid1 => 'initial value']) extends TypedCollection {
        protected function generics(): string|Type|array
        {
            return Type::String;
        }

        protected function keyGenerics(): string|Type|array
        {
            return Uuid::class;
        }
    };

    $collection = $typed->withKeyGenerics();

    expect($collection)->toBeInstanceOf(TypedCollection::class);

    $collection->put($uuid2, 'a string');

    $collection[$uuid3] = 'another string';

    expect($collection->all())->toBe([
        $uuid1 => 'initial value',
        $uuid2 => 'a string',
        $uuid3 => 'another string',
    ]);

    expect(fn() => $collection->put('test', $this))
        ->toThrow(InvalidTypeException::class);
});

test('lazy typed collection with key generics', function () {
    $uuid1 = \Ramsey\Uuid\Uuid::uuid4()->toString();

    $typed = new class([$uuid1 => 'initial value']) extends LazyTypedCollection {
        protected function generics(): string|Type|array
        {
            return Type::String;
        }

        protected function keyGenerics(): string|Type|array
        {
            return Uuid::class;
        }
    };

    $collection = $typed->withKeyGenerics();

    expect($collection)->toBeInstanceOf(LazyTypedCollection::class);

    expect($collection->all())->toBe([
        $uuid1 => 'initial value',
    ]);
});

test('only generics', function () {
    collect([true, 'hello world'])
        ->onlyGenerics(Type::Bool)
        ->withGenerics(Type::Bool);

    collect([[], 'hello world'])
        ->onlyGenerics(Type::Bool)
        ->withGenerics(Type::Bool);

    expect(fn() => collect([true, 'hello world'])
        ->onlyGenerics(Type::Bool)
        ->withGenerics(Type::String))
        ->toThrow(InvalidTypeException::class);
});

test('only key generics', function () {
    collect([0 => 'test', 'hello world' => 'test'])
        ->onlyKeyGenerics(Type::Int)
        ->withKeyGenerics(Type::Int);

    expect(fn() => collect([0 => 'test', 'hello world' => 'test'])
        ->onlyKeyGenerics(Type::String)
        ->withKeyGenerics(Type::Int))
        ->toThrow(InvalidKeyTypeException::class);
});

test('only lazy generics', function () {
    $collection = collect([true, 'hello world'])
        ->lazy()
        ->onlyGenerics(Type::Bool)
        ->withGenerics(Type::Bool);

    $collection->getIterator()->next();

    $collection = collect([[], 'hello world'])
        ->lazy()
        ->onlyGenerics(Type::Bool)
        ->withGenerics(Type::Bool);

    $collection->getIterator()->next();

    $collection = collect([true, 'hello world'])
        ->lazy()
        ->onlyGenerics(Type::Bool)
        ->withGenerics(Type::String);

    expect(fn() => $collection->getIterator()->next())
        ->toThrow(InvalidTypeException::class);
});

test('only key generics lazy', function () {
    $collection = collect([0 => 'test', 'hello world' => 'test'])
        ->lazy()
        ->onlyKeyGenerics(Type::Int)
        ->withGenerics(Type::Int);

    $collection->getIterator()->next();

    $collection = collect([0 => 'test', 'hello world' => 'test'])
        ->lazy()
        ->onlyGenerics(Type::String)
        ->withGenerics(Type::Int);

    expect(fn() => $collection->getIterator()->next())
        ->toThrow(InvalidTypeException::class);
});
