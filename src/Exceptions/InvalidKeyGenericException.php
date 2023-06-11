<?php

namespace Henzeb\Collection\Exceptions;

use Exception;
use Henzeb\Collection\Concerns\HandlesTypeOutput;
use Henzeb\Collection\Enums\Type;

class InvalidKeyGenericException extends Exception
{
    use HandlesTypeOutput;

    public function __construct(mixed $givenType)
    {
        parent::__construct(
            sprintf(
                'Type of `%s` is invalid. Expected one of [%s] or custom generic type.',
                $this->typesToString([$givenType]),
                $this->typestoString(Type::keyables()),
            )
        );
    }
}
