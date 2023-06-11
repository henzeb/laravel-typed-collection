<?php

namespace Henzeb\Collection\Exceptions;

use Exception;
use Henzeb\Collection\Concerns\HandlesTypeOutput;

class InvalidTypeException extends Exception
{
    use HandlesTypeOutput;

    public function __construct(string $class, mixed $item, array $types)
    {
        parent::__construct(
            sprintf(
                '%s: The given value `%s` does not match (one of) the generic type [%s] for this collection.',
                $class,
                $this->getType($item),
                $this->typesToString($types),
            )
        );
    }
}
