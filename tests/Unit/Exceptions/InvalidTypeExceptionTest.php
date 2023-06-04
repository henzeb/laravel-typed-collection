<?php

namespace Henzeb\Collection\Tests\Unit\Exceptions;

use Henzeb\Collection\Exceptions\InvalidTypeException;
use PHPUnit\Framework\TestCase;

class InvalidTypeExceptionTest extends TestCase
{
    public function testMessageContainsGeneric()
    {
        $exception = new InvalidTypeException();
        $this->assertSame(
            'The given value does not match a generic type for this collection.',
            $exception->getMessage()
        );
    }
}
