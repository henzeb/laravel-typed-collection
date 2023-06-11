<?php

namespace Henzeb\Collection\Tests\Unit\Exceptions;

use Henzeb\Collection\Enums\Type;
use Henzeb\Collection\Exceptions\InvalidKeyTypeException;
use Henzeb\Collection\Generics\Uuid;
use PHPUnit\Framework\TestCase;
use stdClass;

class InvalidKeyTypeExceptionTest extends TestCase
{
    public function testMessageContainsGeneric()
    {
        $exception = new InvalidKeyTypeException(self::class, 'hello world', [Type::Bool, Uuid::class]);
        $this->assertSame(
            self::class . ': The given key `string` does not match (one of) the generic types [boolean, Henzeb\Collection\Generics\Uuid] for this collection.',
            $exception->getMessage()
        );

        $exception = new InvalidKeyTypeException(self::class, new stdClass(), [Type::String]);
        $this->assertSame(
            self::class . ': The given key `stdClass` does not match (one of) the generic types [string] for this collection.',
            $exception->getMessage()
        );

        $exception = new InvalidKeyTypeException(stdClass::class, stdClass::class, [Type::Int]);
        $this->assertSame(
            'stdClass: The given key `string` does not match (one of) the generic types [integer] for this collection.',
            $exception->getMessage()
        );
    }
}
