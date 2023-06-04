<?php

namespace Henzeb\Collection\Exceptions;

use Exception;

class InvalidGenericException extends Exception
{
    public function __construct(string $type)
    {
        parent::__construct(
            sprintf(
                'The specified generic type `%s` is invalid or not supported.',
                $type
            )
        );
    }
}
