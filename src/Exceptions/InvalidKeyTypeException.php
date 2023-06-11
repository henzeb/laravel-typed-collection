<?php

namespace Henzeb\Collection\Exceptions;

use Exception;
use Henzeb\Collection\Concerns\HandlesTypeOutput;

class InvalidKeyTypeException extends Exception
{
    use HandlesTypeOutput;

    public function __construct(string $class, mixed $item, array $types)
    {
        parent::__construct(
            sprintf(
                '%s: The given key `%s` does not match (one of) the generic types [%s] for this collection.',
                $class,
                $this->getType($item),
                $this->typesToString($types),
            )
        );
    }
}
