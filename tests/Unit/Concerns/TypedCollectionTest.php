<?php

namespace Henzeb\Collection\Tests\Unit\Concerns;

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
use Orchestra\Testbench\TestCase;

class TypedCollectionTest extends TestCase
{
    use TypedCollection;

    public $typedCollection = null;

    public function testUserModelAllShouldReturnUsersCollection()
    {
        $collection = User::all();

        $this->assertInstanceOf(Users::class, $collection);
        $this->assertInstanceOf(EloquentTypedCollection::class, $collection);
        $this->assertInstanceOf(Collection::class, $collection);

        $this->assertCount(3, $collection);
        $this->assertEquals(1, $collection->first()->id);
        $this->assertEquals('Wally', $collection->get(1)->name);
        $this->assertEquals('Allen-West', $collection->get(2)->last_name);
    }


    public function testShouldThrowErrorWhenInvalidCollectionType()
    {
        $this->typedCollection = self::class;
        $this->expectException(InvalidTypedCollectionException::class);
        $this->expectExceptionMessageMatches('/`TypedCollectionTest`/');

        $this->newCollection();
    }

    public function testShouldThrowErrorWhenNotTypedCollection()
    {
        $this->typedCollection = BaseCollection::class;
        $this->expectException(InvalidTypedCollectionException::class);
        $this->expectExceptionMessageMatches('/`TypedCollectionTest`/');

        $this->newCollection();
    }

    public function testShouldThrowErrorWhenNotEloquentTypedCollection()
    {
        $this->typedCollection = Collection::class;
        $this->expectException(InvalidTypedCollectionException::class);
        $this->expectExceptionMessageMatches('/`TypedCollectionTest`/');

        $this->newCollection();
    }

    public function testShouldThrowErrorWhenTypedLazyCollection()
    {
        $this->typedCollection = LazyCollection::class;
        $this->expectException(InvalidTypedCollectionException::class);
        $this->expectExceptionMessageMatches('/`TypedCollectionTest`/');

        $this->newCollection();
    }

    public function testShouldThrowErrorWhenTypedCollectionOmitted()
    {
        $this->expectException(MissingTypedCollectionException::class);
        $this->expectExceptionMessageMatches('/`TypedCollectionTest`/');

        $this->newCollection();
    }

    public function testShouldReturnTypedCollection()
    {
        $this->typedCollection = Strings::class;

        $this->assertInstanceOf(Strings::class, $this->newCollection());
    }

    public function testShouldReturnLazyTypedCollection()
    {
        $this->typedCollection = Integers::class;

        $this->assertInstanceOf(Integers::class, $this->newCollection());
    }
}
