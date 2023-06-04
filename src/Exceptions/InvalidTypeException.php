<?php

namespace Henzeb\Collection\Exceptions;

use Exception;

class InvalidTypeException extends Exception
{
    public function __construct()
    {
        parent::__construct(
            'The given value does not match a generic type for this collection.'
        );
    }
}
