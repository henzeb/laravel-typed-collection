<?php

use Henzeb\Collection\Exceptions\InvalidKeyGenericException;

test('message contains generic', function () {
    $exception = new InvalidKeyGenericException('hello world');
    expect($exception->getMessage())->toBe('Type of `hello world` is invalid. Expected one of [integer, numeric, string, boolean, NULL] or custom generic type.');

    $exception = new InvalidKeyGenericException(new \stdClass());
    expect($exception->getMessage())->toBe('Type of `unknown` is invalid. Expected one of [integer, numeric, string, boolean, NULL] or custom generic type.');

    $exception = new InvalidKeyGenericException(\stdClass::class);
    expect($exception->getMessage())->toBe('Type of `stdClass` is invalid. Expected one of [integer, numeric, string, boolean, NULL] or custom generic type.');
});
