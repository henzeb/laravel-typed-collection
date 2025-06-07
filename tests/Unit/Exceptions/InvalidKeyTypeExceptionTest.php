<?php

use Henzeb\Collection\Enums\Type;
use Henzeb\Collection\Exceptions\InvalidKeyTypeException;
use Henzeb\Collection\Generics\Uuid;

test('message contains generic', function () {
    $exception = new InvalidKeyTypeException('InvalidKeyTypeExceptionTest', 'hello world', [Type::Bool, Uuid::class]);
    expect($exception->getMessage())->toBe('InvalidKeyTypeExceptionTest: The given key `string` does not match (one of) the generic types [boolean, Henzeb\Collection\Generics\Uuid] for this collection.');

    $exception = new InvalidKeyTypeException('InvalidKeyTypeExceptionTest', new \stdClass(), [Type::String]);
    expect($exception->getMessage())->toBe('InvalidKeyTypeExceptionTest: The given key `stdClass` does not match (one of) the generic types [string] for this collection.');

    $exception = new InvalidKeyTypeException(\stdClass::class, \stdClass::class, [Type::Int]);
    expect($exception->getMessage())->toBe('stdClass: The given key `string` does not match (one of) the generic types [integer] for this collection.');
});
