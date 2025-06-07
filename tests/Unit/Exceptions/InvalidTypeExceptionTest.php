<?php

use Henzeb\Collection\Enums\Type;
use Henzeb\Collection\Exceptions\InvalidTypeException;

test('message contains generic', function () {
    $exception = new InvalidTypeException(
        'Henzeb\Collection\Tests\Unit\Exceptions\InvalidTypeExceptionTest',
        'Hello World',
        ['Henzeb\Collection\Tests\Unit\Exceptions\InvalidTypeExceptionTest', Type::Numeric]
    );
    expect($exception->getMessage())->toBe(
        'Henzeb\Collection\Tests\Unit\Exceptions\InvalidTypeExceptionTest: The given value `string` does not match (one of) the generic type [Henzeb\Collection\Tests\Unit\Exceptions\InvalidTypeExceptionTest, numeric] for this collection.'
    );
});

test('message contains unknown generic', function () {
    $testObject = new class {};
    $exception = new InvalidTypeException(
        'Henzeb\Collection\Tests\Unit\Exceptions\InvalidTypeExceptionTest',
        'Hello World',
        [$testObject]
    );
    expect($exception->getMessage())->toBe(
        'Henzeb\Collection\Tests\Unit\Exceptions\InvalidTypeExceptionTest: The given value `string` does not match (one of) the generic type [unknown] for this collection.'
    );
});


test('message contains generic type of item', function (mixed $item, string $expectedValue) {
    $exception = new InvalidTypeException(
        'test',
        $item,
        []
    );

    expect($exception->getMessage())->toBe(
        'test: The given value `' . $expectedValue . '` does not match (one of) the generic type [] for this collection.'
    );
})->with([
    'string' => ['Hello World', 'string'],
    'numeric' => ['21', 'numeric'],
    'resource' => [STDIN, 'resource'],
    'double' => [1.1, 'double'],
    'array' => [['hello'], 'array'],
    'boolean' => [true, 'boolean'],
    'integer' => [21, 'integer'],
]);
