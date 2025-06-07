<?php

use Henzeb\Collection\Enums\Type;

function getTryFromTestcases(): array
{
    $result = [
        'invalid' => [null, 'failure']
    ];

    foreach (Type::cases() as $case) {
        $name = strtolower($case->name);
        $result[$name] = [$case, $case->name];
        $result[$name . '-upper'] = [$case, strtoupper($case->name)];
        $result[$name . '-lower'] = [$case, $name];
    }

    return $result;
}

test('try from', function (?Type $expects, $tryFrom) {
    expect(Type::tryFrom($tryFrom))->toBe($expects);
})->with(getTryFromTestcases());

test('from value', function (?Type $expects, mixed $from) {
    expect(Type::fromValue($from))->toBe($expects);
})->with([
    'bool' => [Type::Bool, true],
    'string' => [Type::String, 'Hello World'],
    'numeric' => [Type::Numeric, '1.0'],
    'integer' => [Type::Int, 1],
    'double' => [Type::Double, 1.1],
    'array' => [Type::Array, ['Array']],
    'null' => [Type::Null, null],
    'resource' => [Type::Resource, fopen('php://memory', 'r+')],
    'closed-resource' => [null, tap(fopen('php://memory', 'r+'), fn($stdin) => fclose($stdin))],
    'object' => [Type::Object, new \stdClass()],
]);

test('equals', function (Type|null $match, Type $with, bool $expected) {
    expect($with->equals($match))->toBe($expected);
})->with([
    [null, Type::String, false],
    [Type::Int, Type::String, false],
    [Type::String, Type::String, true],
    [Type::Int, Type::Numeric, true],
    [Type::Double, Type::Numeric, true],
    [Type::Numeric, Type::Numeric, true],
    [Type::Numeric, Type::Int, false],
    [Type::Numeric, Type::Double, false],
]);

test('value', function (Type $type, string $expected) {
    expect($type->value())->toBe($expected);
})->with([
    [Type::Bool, 'boolean'],
    [Type::String, 'string'],
    [Type::Int, 'integer'],
    [Type::Double, 'double'],
    [Type::Array, 'array'],
    [Type::Null, 'NULL'],
    [Type::Resource, 'resource'],
    [Type::Object, 'object'],
    [Type::Numeric, 'numeric']
]);
