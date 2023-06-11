<?php

namespace Henzeb\Collection\Tests\Unit\Providers;

use Henzeb\Collection\Enums\Type;
use Henzeb\Collection\Exceptions\InvalidKeyTypeException;
use Henzeb\Collection\Exceptions\InvalidTypeException;
use Henzeb\Collection\Exceptions\MissingGenericsException;
use Henzeb\Collection\Generics\Uuid;
use Henzeb\Collection\LazyTypedCollection;
use Henzeb\Collection\Providers\TypedCollectionProvider;
use Henzeb\Collection\TypedCollection;
use Orchestra\Testbench\TestCase;

class TypedCollectionProviderTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            TypedCollectionProvider::class
        ];
    }

    public function testGetWithGenerics()
    {
        $collection = collect('initial value')->withGenerics('string');

        $this->assertInstanceOf(TypedCollection::class, $collection);

        $collection->add('a string');

        $collection[] = 'another string';

        $this->assertSame(
            [
                'initial value',
                'a string',
                'another string',
            ],
            $collection->all()
        );

        $this->expectException(InvalidTypeException::class);

        $collection->put('test', $this);
    }

    public function testGetWithGenericsWithoutTypes()
    {
        $this->expectException(MissingGenericsException::class);
        collect()->withGenerics();

        $this->expectException(MissingGenericsException::class);
        collect()->lazy()->withGenerics();
    }

    public function testGetWithGenericsLazy()
    {
        $collection = collect(['hello world'])
            ->lazy()
            ->withGenerics('string');

        $this->assertInstanceOf(LazyTypedCollection::class, $collection);

        $this->assertSame(
            [
                'hello world',
            ],
            $collection->all()
        );

        $collection = collect(['hello world', true])
            ->lazy()
            ->withGenerics('string')
            ->getIterator();


        $this->expectException(InvalidTypeException::class);

        $collection->next();
    }

    public function testTypedCollectionWithGenerics()
    {
        $typed = new class(['initial value']) extends TypedCollection {
            protected function generics(): string|Type|array
            {
                return Type::String;
            }
        };

        $collection = $typed->withGenerics();

        $this->assertInstanceOf(TypedCollection::class, $collection);

        $collection->add('a string');

        $collection[] = 'another string';

        $this->assertSame(
            [
                'initial value',
                'a string',
                'another string',
            ],
            $collection->all()
        );

        $this->expectException(InvalidTypeException::class);

        $collection->put('test', $this);
    }

    public function testTypedCollectionWithKeyGenerics()
    {
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

        $this->assertInstanceOf(TypedCollection::class, $collection);

        $collection->put($uuid2, 'a string');

        $collection[$uuid3] = 'another string';

        $this->assertSame(
            [
                $uuid1 => 'initial value',
                $uuid2 => 'a string',
                $uuid3 => 'another string',
            ],
            $collection->all()
        );

        $this->expectException(InvalidTypeException::class);

        $collection->put('test', $this);
    }

    public function testLazyTypedCollectionWithKeyGenerics()
    {
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

        $this->assertInstanceOf(LazyTypedCollection::class, $collection);

        $this->assertSame(
            [
                $uuid1 => 'initial value',
            ],
            $collection->all()
        );
    }

    public function testOnlyGenerics(): void
    {
        collect([true, 'hello world'])
            ->onlyGenerics(Type::Bool)
            ->withGenerics(Type::Bool);

        collect([[], 'hello world'])
            ->onlyGenerics(Type::Bool)
            ->withGenerics(Type::Bool);

        $this->expectException(InvalidTypeException::class);

        collect([true, 'hello world'])
            ->onlyGenerics(Type::Bool)
            ->withGenerics(Type::String);
    }

    public function testOnlyKeyGenerics(): void
    {
        collect([0 => 'test', 'hello world' => 'test'])
            ->onlyKeyGenerics(Type::Int)
            ->withKeyGenerics(Type::Int);

        $this->expectException(InvalidKeyTypeException::class);

        collect([0 => 'test', 'hello world' => 'test'])
            ->onlyKeyGenerics(Type::String)
            ->withKeyGenerics(Type::Int);
    }

    public function testOnlyLazyGenerics(): void
    {
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

        $this->expectException(InvalidTypeException::class);

        $collection->getIterator()->next();
    }

    public function testOnlyKeyGenericsLazy(): void
    {
        $collection = collect([0 => 'test', 'hello world' => 'test'])
            ->lazy()
            ->onlyKeyGenerics(Type::Int)
            ->withGenerics(Type::Int);

        $collection->getIterator()->next();

        $this->expectException(InvalidTypeException::class);

        $collection = collect([0 => 'test', 'hello world' => 'test'])
            ->onlyGenerics(Type::String)
            ->withGenerics(Type::Int);
        $collection->getIterator()->next();
    }
}
