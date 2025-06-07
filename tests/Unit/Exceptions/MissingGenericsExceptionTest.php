<?php

use Henzeb\Collection\Exceptions\MissingGenericsException;

test('message contains generic', function () {
    $exception = new MissingGenericsException();
    expect($exception->getMessage())->toBe('At least one generic type must be specified.');
});
