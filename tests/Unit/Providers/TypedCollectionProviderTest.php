<?php

namespace Henzeb\Collection\Tests\Unit\Providers;

use Henzeb\Collection\Enums\Type;
use Henzeb\Collection\Exceptions\InvalidTypeException;
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
}
