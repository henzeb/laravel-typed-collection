<?php

namespace Henzeb\Collection\Tests\Unit\Providers;

use Henzeb\Collection\Enums\Type;
use Henzeb\Collection\Exceptions\InvalidTypeException;
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
}
