<?php

use Henzeb\Collection\Exceptions\InvalidTypedCollectionException;

test('message', function () {
    $exception = new InvalidTypedCollectionException('InvalidTypedCollectionExceptionTest');
    expect($exception->getMessage())->toBe('Invalid typed collection for `InvalidTypedCollectionExceptionTest`. Specify a typed collection in $typedCollection, or remove trait.');

    $exception = new InvalidTypedCollectionException('hello world');
    expect($exception->getMessage())->toBe('Invalid typed collection for `hello world`. Specify a typed collection in $typedCollection, or remove trait.');
});
