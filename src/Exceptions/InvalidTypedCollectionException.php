<?php

namespace Henzeb\Collection\Exceptions;

use Exception;

class InvalidTypedCollectionException extends Exception
{
    public function __construct(string $model)
    {
        parent::__construct(
            sprintf(
                'Invalid typed collection for `%s`. Specify a typed collection in $typedCollection, or remove trait.',
                class_basename($model)
            )
        );
    }
}
