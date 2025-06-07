<?php

use Henzeb\Collection\Exceptions\InvalidGenericException;

test('message contains generic', function () {
    $exception = new InvalidGenericException('hello world');
    expect($exception->getMessage())->toBe('The specified generic type `hello world` is invalid or not supported.');

    $exception = new InvalidGenericException('another planet');
    expect($exception->getMessage())->toBe('The specified generic type `another planet` is invalid or not supported.');
});
