<?php

use Henzeb\Collection\Exceptions\MissingTypedCollectionException;

test('message', function () {
    $exception = new MissingTypedCollectionException('MissingTypedCollectionExceptionTest');
    expect($exception->getMessage())->toBe('Missing typed collection for `MissingTypedCollectionExceptionTest`. Specify one in $typedCollection, or remove trait.');

    $exception = new MissingTypedCollectionException('another planet');
    expect($exception->getMessage())->toBe('Missing typed collection for `another planet`. Specify one in $typedCollection, or remove trait.');
});
