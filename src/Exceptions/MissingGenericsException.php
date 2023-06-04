<?php

namespace Henzeb\Collection\Exceptions;

use Exception;

class MissingGenericsException extends Exception
{
    public function __construct()
    {
        parent::__construct(
            'At least one generic type must be specified.'
        );
    }
}
