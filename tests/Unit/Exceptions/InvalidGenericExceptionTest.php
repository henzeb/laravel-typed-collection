<?php

namespace Henzeb\Collection\Tests\Unit\Exceptions;

use Henzeb\Collection\Exceptions\InvalidGenericException;
use PHPUnit\Framework\TestCase;

class InvalidGenericExceptionTest extends TestCase
{
    public function testMessageContainsGeneric()
    {
        $exception = new InvalidGenericException('hello world');
        $this->assertSame(
            'The specified generic type `hello world` is invalid or not supported.',
            $exception->getMessage()
        );

        $exception = new InvalidGenericException('another planet');
        $this->assertSame(
            'The specified generic type `another planet` is invalid or not supported.',
            $exception->getMessage()
        );
    }
}
