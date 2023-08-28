<?php

namespace Henzeb\Collection\Tests\Unit\Generics;

use Closure;
use Generator;
use Henzeb\Collection\Generics\Json;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use JsonSerializable;
use PHPUnit\Framework\TestCase;
use Throwable;

class JsonTest extends TestCase
{
    public function testMemoization()
    {
        $useValidate = !function_exists('json_validate'); // false
        $this->useNative($useValidate); // set to false

        try {
            Json::matchesType(json_encode([]));
        } catch (Throwable) {
        }

        $this->assertNativeSet($useValidate);

    }

    public function testJsonValidates(): void
    {
        $this->useNative(false);

        $this->assertTrue(
            Json::matchesType(
                json_encode(['hello' => 'world'])
            )
        );
    }

    public function testJsonValidatesNative(): void
    {
        $this->assertTrue(
            Json::matchesType(
                json_encode(['hello' => 'world'])
            )
        );
    }

    public function testJsonValidatesDepth(): void
    {
        $this->useNative(false);
        $this->assertTrue(
            Json::matchesType(
                json_encode(
                    $this->generateArray(512)
                )
            )
        );

        $this->assertFalse(
            Json::matchesType(
                json_encode(
                    $this->generateArray(513)
                )
            )
        );
    }

    function generateArray($depth)
    {
        if ($depth === 0) {
            return [];
        }
        $array = 1;
        for ($i = $depth; $i > 1; $i--) {
            $array = [$array];
        }

        return $array;
    }

    public function testJsonFailsOnNonString(): void
    {
        $this->useNative(false);

        $this->assertFalse(Json::matchesType(['hello' => 'world']));
    }

    public function testJsonFailsOnNonStringNative(): void
    {
        $this->assertFalse(Json::matchesType(['hello' => 'world']));
    }

    public function testJsonFailsOnNotAJsonString(): void
    {
        $this->useNative(false);

        $this->assertFalse(Json::matchesType('Not a Json'));
    }

    public function testJsonFailsOnNotAJsonNative(): void
    {
        $this->assertFalse(Json::matchesType('Not a Json'));
    }

    public function testJsonFailsOnInvalidJson(): void
    {
        $this->useNative(false);
        $this->assertFalse(
            Json::matchesType(
                ltrim(json_encode(['hello' => 'world']), '{')
            )
        );
    }

    public function testJsonFailsOnInvalidJsonNative(): void
    {
        $this->assertFalse(
            Json::matchesType(
                ltrim(json_encode(['hello' => 'world']), '{')
            )
        );
    }

    public function testTypecast()
    {
        $expected = json_encode(['regular' => 'array']);
        $this->assertEquals($expected, Json::castType(['regular' => 'array']));
        $this->assertEquals($expected, Json::castType(collect(['regular' => 'array'])));

        $this->assertEquals($expected, Json::castType(
            new class implements Arrayable {
                public function toArray()
                {
                    return ['regular' => 'array'];
                }
            }
        ));

        $this->assertEquals($expected, Json::castType(
            new class implements JsonSerializable {
                public function jsonSerialize(): mixed
                {
                    return ['regular' => 'array'];
                }
            }
        ));

        $this->assertEquals($expected, Json::castType(
            new class implements Jsonable {
                public function toJson($options = 0): string
                {
                    return json_encode(['regular' => 'array']);
                }
            }
        ));


        $this->assertEquals($expected, Json::castType(
            (function (): Generator {
                yield 'regular' => 'array';
            })()
        ));
    }

    public function useNative(?bool $useJsonValidate)
    {
        Closure::bind(function (?bool $useNative) {
            self::$useJsonValidate = $useNative;
        }, null, Json::class)(
            $useJsonValidate
        );
    }

    public function assertNativeSet(bool $expected): void
    {
        $native = Closure::bind(function () {
            return self::$useJsonValidate;
        }, null, Json::class)();

        $this->assertEquals($expected, $native);
    }

    protected function tearDown(): void
    {
        $this->useNative(null);
    }
}
