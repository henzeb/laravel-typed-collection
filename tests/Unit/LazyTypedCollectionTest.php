<?php

namespace Henzeb\Collection\Tests\Unit;

use Henzeb\Collection\Enums\Type;
use Henzeb\Collection\Exceptions\InvalidGenericException;
use Henzeb\Collection\Exceptions\InvalidTypeException;
use Henzeb\Collection\Exceptions\MissingGenericsException;
use Henzeb\Collection\LazyTypedCollection;
use PHPUnit\Framework\TestCase;

class LazyTypedCollectionTest extends TestCase
{
    public function testValidatesInvalidGenerics()
    {
        $this->expectException(InvalidGenericException::class);

        new class extends LazyTypedCollection {
            protected function generics(): string|Type|array
            {
                return ['failure'];
            }
        };
    }

    public function testValidatesEmptyGenerics()
    {
        $this->expectException(MissingGenericsException::class);

        new class extends LazyTypedCollection {
            protected function generics(): string|Type|array
            {
                return [];
            }
        };
    }

    public function testValidatesGenerics()
    {
        $this->expectNotToPerformAssertions();

        new class extends LazyTypedCollection {
            protected function generics(): string|Type|array
            {
                return Type::String;
            }
        };
    }

    public function testDoesNotValidateTypesOnNewInstance()
    {
        $this->expectNotToPerformAssertions();

        new class([$this]) extends LazyTypedCollection {
            protected function generics(): string|Type|array
            {
                return Type::String;
            }
        };
    }

    public function testDoesValidateTypesWhenRunning()
    {
        $this->expectException(InvalidTypeException::class);
        $lazy = new class([$this]) extends LazyTypedCollection {
            protected function generics(): string|Type|array
            {
                return Type::String;
            }
        };

        $lazy->each(fn() => true);
    }

    public function testDoesNotValidateTypeIfNotFetched()
    {
        $lazy = new class(['myKey' => 'string', $this]) extends LazyTypedCollection {
            protected function generics(): string|Type|array
            {
                return Type::String;
            }
        };

        foreach ($lazy as $key => $item) {
            $this->assertSame('myKey', $key);
            $this->assertEquals('string', $item);
            break;
        }
    }
}
