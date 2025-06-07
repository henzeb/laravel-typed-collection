<?php

use Henzeb\Collection\Concerns\TypedCollection;
use Henzeb\Collection\EloquentTypedCollection;
use Henzeb\Collection\Exceptions\InvalidTypedCollectionException;
use Henzeb\Collection\Exceptions\MissingTypedCollectionException;
use Henzeb\Collection\Lazy\Integers;
use Henzeb\Collection\Tests\Stubs\Eloquent\User;
use Henzeb\Collection\Tests\Stubs\Eloquent\Users;
use Henzeb\Collection\Typed\Strings;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as BaseCollection;
use Illuminate\Support\LazyCollection;

class TestTypedCollectionClass
{
    use TypedCollection;
    
    public $typedCollection = null;
}

test('user model all should return users collection', function () {
    $collection = User::all();

    expect($collection)->toBeInstanceOf(Users::class);
    expect($collection)->toBeInstanceOf(EloquentTypedCollection::class);
    expect($collection)->toBeInstanceOf(Collection::class);

    expect($collection)->toHaveCount(3);
    expect($collection->first()->id)->toBe(1);
    expect($collection->get(1)->name)->toBe('Wally');
    expect($collection->get(2)->last_name)->toBe('Allen-West');
});

test('should throw error when invalid collection type', function () {
    $testClass = new TestTypedCollectionClass();
    $testClass->typedCollection = TestTypedCollectionClass::class;
    
    expect(fn() => $testClass->newCollection())
        ->toThrow(InvalidTypedCollectionException::class, 'TestTypedCollectionClass');
});

test('should throw error when not typed collection', function () {
    $testClass = new TestTypedCollectionClass();
    $testClass->typedCollection = BaseCollection::class;
    
    expect(fn() => $testClass->newCollection())
        ->toThrow(InvalidTypedCollectionException::class, 'TestTypedCollectionClass');
});

test('should throw error when not eloquent typed collection', function () {
    $testClass = new TestTypedCollectionClass();
    $testClass->typedCollection = Collection::class;
    
    expect(fn() => $testClass->newCollection())
        ->toThrow(InvalidTypedCollectionException::class, 'TestTypedCollectionClass');
});

test('should throw error when typed lazy collection', function () {
    $testClass = new TestTypedCollectionClass();
    $testClass->typedCollection = LazyCollection::class;
    
    expect(fn() => $testClass->newCollection())
        ->toThrow(InvalidTypedCollectionException::class, 'TestTypedCollectionClass');
});

test('should throw error when typed collection omitted', function () {
    $testClass = new TestTypedCollectionClass();
    
    expect(fn() => $testClass->newCollection())
        ->toThrow(MissingTypedCollectionException::class, 'TestTypedCollectionClass');
});

test('should return typed collection', function () {
    $testClass = new TestTypedCollectionClass();
    $testClass->typedCollection = Strings::class;

    expect($testClass->newCollection())->toBeInstanceOf(Strings::class);
});

test('should return lazy typed collection', function () {
    $testClass = new TestTypedCollectionClass();
    $testClass->typedCollection = Integers::class;

    expect($testClass->newCollection())->toBeInstanceOf(Integers::class);
});
