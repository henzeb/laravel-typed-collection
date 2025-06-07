<?php

use Henzeb\Collection\Exceptions\MissingKeyGenericsException;

test('message contains generic', function () {
    $exception = new MissingKeyGenericsException();
    expect($exception->getMessage())->toBe('At least one generic key type must be specified.');
});
