<?php

namespace Henzeb\Collection\Tests\Unit;

use Henzeb\Collection\Enums\Type;
use Henzeb\Collection\Exceptions\InvalidGenericException;
use Henzeb\Collection\Exceptions\InvalidKeyTypeException;
use Henzeb\Collection\Exceptions\InvalidTypeException;
use Henzeb\Collection\Exceptions\MissingGenericsException;
use Henzeb\Collection\Exceptions\MissingKeyGenericsException;
use Henzeb\Collection\Generics\Uuid;
use Henzeb\Collection\Lazy\Strings;
use Henzeb\Collection\LazyTypedCollection;
use Henzeb\Collection\Support\GenericsLazyCollection;
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

        $collection = new GenericsLazyCollection(
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
        new class extends LazyTypedCollection {
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

    public function testAllowChunks(): void
    {
        $collection = new class(['hello', 'world', '!']) extends LazyTypedCollection {
            protected function generics(): Type
            {
                return Type::String;
            }
        };

        $chunks = $collection->chunk(2);

        $this->assertCount(2, $chunks);

        foreach ($chunks as $chunk) {
            $this->assertInstanceOf($collection::class, $chunk);
        }

        $this->assertSame(['hello', 'world'], $chunks->first()->all());
        $this->assertSame([2 => '!'], $chunks->last()->all());
    }

    public function testAllowChunkWhile(): void
    {
        $collection = new class(['hello', 'world', '!']) extends LazyTypedCollection {
            protected function generics(): Type
            {
                return Type::String;
            }
        };

        $chunks = $collection->chunkWhile(fn() => false)->all();

        $this->assertCount(3, $chunks);

        foreach ($chunks as $chunk) {
            $this->assertInstanceOf($collection::class, $chunk);
        }

        $this->assertSame(['hello'], $chunks[0]->all());
        $this->assertSame([1 => 'world'], $chunks[1]->all());
        $this->assertSame([2 => '!'], $chunks[2]->all());
    }

    public function testAllowMapping()
    {
        $this->assertEquals(
            Strings::make(['string', 'another'])->map(fn(string $string) => $string === 'string')->toArray(),
            [true, false]
        );
    }

    public function testAllowWithKeys()
    {
        $this->assertEquals(
            Strings::make(['string', 'another'])->mapWithKeys(
                fn(string $string, int $key) => [$key + 1 => $string === 'string']
            )->toArray(),
            [1 => true, 2 => false]
        );
    }

    public function testAllowMapToDictonairy()
    {
        $collection = new class([
            [
                'name' => 'John Doe',
                'department' => 'Sales',
            ],
            [
                'name' => 'Jane Doe',
                'department' => 'Sales',
            ],
            [
                'name' => 'Johnny Doe',
                'department' => 'Marketing',
            ]
        ]) extends LazyTypedCollection {
            protected function generics(): Type
            {
                return Type::Array;
            }

            protected function keyGenerics(): string|Type|array
            {
                return Type::Int;
            }
        };

        $this->assertEquals(
            $collection->mapToDictionary(
                function (array $item) {
                    return [$item['department'] => $item['name']];
                }
            )->toArray(),
            [
                'Sales' => [
                    'John Doe',
                    'Jane Doe'
                ],
                'Marketing' => [
                    'Johnny Doe'
                ]
            ]
        );
    }
}
