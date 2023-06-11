<?php

namespace Henzeb\Collection\Tests\Unit\Generics;

use Closure;
use Henzeb\Collection\Generics\Json;
use PHPUnit\Framework\TestCase;

class JsonTest extends TestCase
{
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

    public function useNative(?bool $useNative)
    {
        Closure::bind(function (?bool $useNative) {
            self::$native = $useNative;
        }, null, Json::class)(
            $useNative
        );
    }

    protected function tearDown(): void
    {
        $this->useNative(null);
    }
}
