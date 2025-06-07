<?php

use Henzeb\Collection\Generics\Json;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;

function useNative(?bool $useJsonValidate)
{
    \Closure::bind(function (?bool $useNative) {
        self::$useJsonValidate = $useNative;
    }, null, Json::class)(
        $useJsonValidate
    );
}

function assertNativeSet(bool $expected): void
{
    $native = \Closure::bind(function () {
        return self::$useJsonValidate;
    }, null, Json::class)();

    expect($native)->toBe($expected);
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

afterEach(function () {
    useNative(null);
});

test('memoization', function () {
    $useValidate = !function_exists('json_validate');
    useNative($useValidate);

    try {
        Json::matchesType(json_encode([]));
    } catch (\Throwable) {
    }

    assertNativeSet($useValidate);
});

test('json validates', function () {
    useNative(false);

    expect(Json::matchesType(json_encode(['hello' => 'world'])))->toBeTrue();
});

test('json validates native', function () {
    expect(Json::matchesType(json_encode(['hello' => 'world'])))->toBeTrue();
});

test('json validates depth', function () {
    useNative(false);
    
    expect(Json::matchesType(json_encode(generateArray(512))))->toBeTrue();
    expect(Json::matchesType(json_encode(generateArray(513))))->toBeFalse();
});

test('json fails on non string', function () {
    useNative(false);

    expect(Json::matchesType(['hello' => 'world']))->toBeFalse();
});

test('json fails on non string native', function () {
    expect(Json::matchesType(['hello' => 'world']))->toBeFalse();
});

test('json fails on not a json string', function () {
    useNative(false);

    expect(Json::matchesType('Not a Json'))->toBeFalse();
});

test('json fails on not a json native', function () {
    expect(Json::matchesType('Not a Json'))->toBeFalse();
});

test('json fails on invalid json', function () {
    useNative(false);
    
    expect(Json::matchesType(ltrim(json_encode(['hello' => 'world']), '{')))->toBeFalse();
});

test('json fails on invalid json native', function () {
    expect(Json::matchesType(ltrim(json_encode(['hello' => 'world']), '{')))->toBeFalse();
});

test('typecast', function () {
    $expected = json_encode(['regular' => 'array']);
    
    expect(Json::castType(['regular' => 'array']))->toBe($expected);
    expect(Json::castType(collect(['regular' => 'array'])))->toBe($expected);

    expect(Json::castType(
        new class implements Arrayable {
            public function toArray()
            {
                return ['regular' => 'array'];
            }
        }
    ))->toBe($expected);

    expect(Json::castType(
        new class implements \JsonSerializable {
            public function jsonSerialize(): mixed
            {
                return ['regular' => 'array'];
            }
        }
    ))->toBe($expected);

    expect(Json::castType(
        new class implements Jsonable {
            public function toJson($options = 0): string
            {
                return json_encode(['regular' => 'array']);
            }
        }
    ))->toBe($expected);

    expect(Json::castType(
        (function (): \Generator {
            yield 'regular' => 'array';
        })()
    ))->toBe($expected);
});
