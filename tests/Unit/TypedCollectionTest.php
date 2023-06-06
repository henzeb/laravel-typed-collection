<?php

namespace Henzeb\Collection\Tests\Unit;

use Henzeb\Collection\Enums\Type;
use Henzeb\Collection\Exceptions\InvalidGenericException;
use Henzeb\Collection\Exceptions\InvalidTypeException;
use Henzeb\Collection\Exceptions\MissingGenericsException;
use Henzeb\Collection\LazyTypedCollection;
use Henzeb\Collection\TypedCollection;
use Illuminate\Support\LazyCollection;
use PHPUnit\Framework\TestCase;
use TypeError;

class TypedCollectionTest extends TestCase
{
    public function testEmptyGenerics()
    {
        $this->expectException(MissingGenericsException::class);
        new class([]) extends TypedCollection {
            protected function generics(): string|Type|array
            {
                return [];
            }
        };
    }

    public function testInvalidGenerics()
    {
        $this->expectException(InvalidGenericException::class);

        new class([]) extends TypedCollection {
            protected function generics(): string|Type|array
            {
                return 'RandomDoesNotExistString';
            }
        };
    }

    public function testInvalidGenericsWithObject()
    {
        $this->expectException(InvalidGenericException::class);

        $this->expectExceptionMessageMatches('/`object`/');

        new class([]) extends TypedCollection {
            protected function generics(): string|Type|array
            {
                return [$this];
            }
        };
    }

    public function testInvalidGenericsWhereOneIsValid()
    {
        $this->expectException(InvalidGenericException::class);

        new class([]) extends TypedCollection {
            protected function generics(): string|Type|array
            {
                return ['bool', Type::String, 'RandomDoesNotExistString'];
            }
        };
    }

    public function testInvalidGenericsWhereOneIsInvalid()
    {
        $this->expectException(InvalidGenericException::class);

        new class([]) extends TypedCollection {
            protected function generics(): string|Type|array
            {
                return [self::class, 'stringed'];
            }
        };
    }

    public function testValidGenericsClass()
    {
        $this->expectNotToPerformAssertions();

        new class([]) extends TypedCollection {
            protected function generics(): string|Type|array
            {
                return [self::class];
            }
        };
    }

    public function testValidGenericsTypeString()
    {
        $this->expectNotToPerformAssertions();

        new class([]) extends TypedCollection {
            protected function generics(): string|Type|array
            {
                return ['bool'];
            }
        };
    }

    public function testValidGenericsWithType()
    {
        $this->expectNotToPerformAssertions();

        new class([]) extends TypedCollection {
            protected function generics(): string|Type|array
            {
                return [Type::Numeric];
            }
        };
    }

    public function testValidateItems()
    {
        $collection = new class(['Hello World']) extends TypedCollection {
            protected function generics(): string|Type|array
            {
                return ['string'];
            }
        };

        $collection2 = new class(['Hello World', $this]) extends TypedCollection {
            protected function generics(): string|Type|array
            {
                return ['string', TypedCollectionTest::class];
            }
        };

        $this->assertSame(['Hello World'], $collection->all());
        $this->assertSame(['Hello World', $this],
            $collection2->all());
    }

    public function testValidateItemsFail()
    {
        $this->expectException(InvalidTypeException::class);

        new class([$this]) extends TypedCollection {
            protected function generics(): string|Type|array
            {
                return [TypedCollection::class];
            }
        };
    }

    public function testValidateNullFail()
    {
        $this->expectException(InvalidTypeException::class);

        new class([null]) extends TypedCollection {
            protected function generics(): string|Type|array
            {
                return [TypedCollection::class];
            }
        };
    }

    public function testAddFail()
    {
        $this->expectException(InvalidTypeException::class);

        (new class() extends TypedCollection {
            protected function generics(): string|Type|array
            {
                return ['string'];
            }
        })->add($this);
    }

    public function testAddSuccess()
    {
        $collection = (new class() extends TypedCollection {
            protected function generics(): string|Type|array
            {
                return [TypedCollectionTest::class];
            }
        })->add($this);

        $this->assertSame([$this], $collection->all());
    }

    public function testPushFail()
    {
        $this->expectException(InvalidTypeException::class);

        (new class() extends TypedCollection {
            protected function generics(): string|Type|array
            {
                return ['string'];
            }
        })->push('text', $this);
    }

    public function testPushSuccess()
    {
        $collection = (new class() extends TypedCollection {
            protected function generics(): string|Type|array
            {
                return [TypedCollectionTest::class];
            }
        })->push($this);

        $this->assertSame([$this], $collection->all());
    }

    public function testPrependFail()
    {
        $this->expectException(InvalidTypeException::class);

        (new class() extends TypedCollection {
            protected function generics(): string|Type|array
            {
                return ['string'];
            }
        })->prepend($this);
    }

    public function testPrependSuccess()
    {
        $collection = (new class() extends TypedCollection {
            protected function generics(): string|Type|array
            {
                return [TypedCollectionTest::class];
            }
        })->prepend($this);

        $this->assertSame([$this], $collection->all());
    }

    public function testCollect()
    {
        $collection = new class([$this]) extends TypedCollection {
            protected function generics(): string|Type|array
            {
                return [TypedCollectionTest::class];
            }
        };
        $collectionActual = $collection->collect();
        $collectionActual->add($this);
        $this->assertSame([$this, $this], $collectionActual->all());

        $this->assertNotSame($collection, $collectionActual);
    }

    public function testLazy(): void
    {
        $typed = new class(['hello world']) extends TypedCollection {
            protected function generics(): string|Type|array
            {
                return Type::String;
            }
        };

        $lazy = $typed->lazy();

        $this->assertSame(LazyCollection::class, $lazy::class);

        $this->assertSame(['hello world'], $lazy->all());
    }

    public function testLazyFailsWithSelfClass(): void
    {
        $typed = new class(['hello world']) extends TypedCollection {
            protected function generics(): string|Type|array
            {
                return Type::String;
            }

            protected function lazyClass(): string
            {
                return self::class;
            }
        };

        $this->expectException(TypeError::class);

        $typed->lazy();
    }

    public function testLazyWithCustomClass(): void
    {
        $lazyClass = new class() extends LazyTypedCollection {
            protected function generics(): string|Type|array
            {
                return Type::String;
            }
        };

        $typed = new class(['hello world']) extends TypedCollection {
            public string $lazyClass = '';

            protected function generics(): string|Type|array
            {
                return Type::String;
            }

            protected function lazyClass(): string
            {
                return $this->lazyClass;
            }
        };

        $typed->lazyClass = $lazyClass::class;

        $lazy = $typed->lazy();

        $this->assertSame($lazy::class, $lazyClass::class);

        $this->assertSame(['hello world'], $lazy->all());
    }
}
