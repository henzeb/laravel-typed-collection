<?php

namespace Henzeb\Collection\Exceptions;

use Exception;

class MissingTypedCollectionException extends Exception
{
    public function __construct(string $model)
    {
        parent::__construct(
            sprintf(
                'Missing typed collection for `%s`. Specify one in $typedCollection, or remove trait.',
                class_basename($model)
            )
        );
    }
}
