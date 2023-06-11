<?php

namespace Henzeb\Collection\Exceptions;

use Exception;

class MissingKeyGenericsException extends Exception
{
    public function __construct()
    {
        parent::__construct(
            'At least one generic key type must be specified.'
        );
    }
}
