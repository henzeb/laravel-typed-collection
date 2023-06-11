<?php

namespace Henzeb\Collection\Tests\Unit;

use Henzeb\Collection\Contracts\GenericType;
use Henzeb\Collection\Enums\Type;
use Henzeb\Collection\Exceptions\InvalidGenericException;
use Henzeb\Collection\Exceptions\InvalidKeyGenericException;
use Henzeb\Collection\Exceptions\InvalidKeyTypeException;
use Henzeb\Collection\Exceptions\InvalidTypeException;
use Henzeb\Collection\Exceptions\MissingGenericsException;
use Henzeb\Collection\Exceptions\MissingKeyGenericsException;
use Henzeb\Collection\Generics\Json;
use Henzeb\Collection\Generics\Uuid;
use Henzeb\Collection\LazyTypedCollection;
use Henzeb\Collection\Support\GenericsCollection;
use Henzeb\Collection\TypedCollection;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Support\LazyCollection;
use PHPUnit\Framework\TestCase;
use stdClass;
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

    public function testInterfaceAsGeneric()
    {
        $this->expectNotToPerformAssertions();

        $collection = new class extends TypedCollection {
            protected function generics(): string|Type|array
            {
                return ShouldBeUnique::class;
            }
        };

        $collection->add(
            new class implements ShouldBeUnique {

            }
        );
    }

    public function testCustomGenericType(): void
    {
        $genericType = new class implements GenericType {
            public static function matchesType(mixed $item): bool
            {
                return true;
            }
        };

        $collection = new class($genericType::class, ['Hello World'],) extends TypedCollection {
            public function __construct(private string $genericType, $items = [])
            {
                parent::__construct($items);
            }

            protected function generics(): string|Type|array
            {
                return $this->genericType;
            }
        };

        $this->assertSame(['Hello World'], $collection->all());
    }

    public function testCustomGenericTypeFail(): void
    {
        $genericType = new class implements GenericType {
            public static function matchesType(mixed $item): bool
            {
                return false;
            }
        };

        $this->expectException(InvalidTypeException::class);

        new class($genericType::class, ['Hello World']) extends TypedCollection {
            public function __construct(private string $genericType, $items = [])
            {
                parent::__construct($items);
            }

            protected function generics(): string|Type|array
            {
                return $this->genericType;
            }
        };
    }

    public function testGenericKeyValidation()
    {
        $this->expectNotToPerformAssertions();
        foreach (Type::keyables() + [Uuid::class] as $keyable) {
            new GenericsCollection([], Type::String, $keyable);
        }

        new GenericsCollection(
            [],
            Type::String,
            [Type::String, Uuid::class, Json::class]
        );
    }

    public function testGenericKeyFailAfterSuccessWithType()
    {
        $this->expectException(InvalidKeyGenericException::class);
        new GenericsCollection(
            [],
            Type::String,
            [Type::String, $this::class, Json::class]
        );
    }

    public function testGenericKeyFailAfterSuccessWithGenericType()
    {
        $this->expectException(InvalidKeyGenericException::class);
        new GenericsCollection(
            [],
            Type::String,
            [Json::class, 'not a valid type']
        );
    }

    public function testGenericKeyAcceptsStringedTypes()
    {
        $this->expectNotToPerformAssertions();
        new GenericsCollection(
            [],
            Type::String,
            ['string', 'int', 'bool']
        );
    }

    public function testInvalidGenericKeyString()
    {
        $this->expectException(InvalidKeyGenericException::class);

        new GenericsCollection([], Type::String, self::class);
    }

    public function testInvalidGenericKeyObject()
    {
        $this->expectException(InvalidKeyTypeException::class);

        $collection = new GenericsCollection([], Type::String, Type::String);

        $collection->put(new stdClass(), 'test');
    }

    public static function providesKeyableTestcases(): array
    {
        return [
            'int' => [0],
            'numeric' => ['12'],
            'String' => ['hello'],
            'Bool' => [true],
            'Null' => [null],
            'Uuid' => [\Ramsey\Uuid\Uuid::uuid4()->toString(), Uuid::class],
            'mixed-int' => [1, [Type::Int, Type::String]],
            'mixed-string' => ['1', [Type::Int, Type::String]],
            'int-fail' => [0, Type::String, true],
            'numeric-fail' => ['12', Type::String, true],
            'String-fail' => ['hello', Type::Bool, true],
            'Bool-fail' => [true, Type::String, true],
            'Null-fail' => [null, Type::String, true],
            'Uuid-fail' => ['ohoh', Uuid::class, true]
        ];
    }

    /**
     * @param mixed $key
     * @param Type|string|array|null $generics
     * @param bool $exception
     * @return void
     *
     * @dataProvider providesKeyableTestcases
     */
    public function testKeyValidation(
        mixed $key,
        Type|string|array $generics = null,
        bool $exception = false
    ): void {
        $key = is_null($key) ? (int)$key : $key;

        if ($exception) {
            $this->expectException(InvalidKeyTypeException::class);
        }

        $collection = new GenericsCollection(
            [$key => 'world'],
            Type::String,
            $generics,
        );

        $this->assertEquals(
            [$key => 'world'],
            $collection->all()
        );
    }

    public function testMissingKeyGenerics()
    {
        $this->expectException(MissingKeyGenericsException::class);
        new class extends TypedCollection {
            protected function generics(): string|Type|array
            {
                return Type::String;
            }

            protected function keyGenerics(): string|Type|array
            {
                return [];
            }
        };
    }

    public function testPushKeyGenericsValidation(): void
    {
        $collection = (new GenericsCollection([], Type::String))
            ->push('hello', 'world');

        $this->assertSame(['hello', 'world'], $collection->all());

        $this->expectException(InvalidKeyTypeException::class);

        (new GenericsCollection([], Type::String, Type::String))
            ->push('hello', 'world');
    }

    public function testAllowMixed(): void
    {
        $expected = ['da', 0, true, 1.1, new stdClass()];
        $collecton = new GenericsCollection(
            $expected,
            Type::Mixed
        );

        $this->assertSame($expected, $collecton->all());
    }
}
