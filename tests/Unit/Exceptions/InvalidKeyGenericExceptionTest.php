<?php

namespace Henzeb\Collection\Tests\Unit\Exceptions;

use Henzeb\Collection\Exceptions\InvalidKeyGenericException;
use PHPUnit\Framework\TestCase;
use stdClass;

class InvalidKeyGenericExceptionTest extends TestCase
{
    public function testMessageContainsGeneric()
    {
        $exception = new InvalidKeyGenericException('hello world');
        $this->assertSame(
            'Type of `hello world` is invalid. Expected one of [integer, numeric, string, boolean, NULL] or custom generic type.',
            $exception->getMessage()
        );

        $exception = new InvalidKeyGenericException(new stdClass());
        $this->assertSame(
            'Type of `unknown` is invalid. Expected one of [integer, numeric, string, boolean, NULL] or custom generic type.',
            $exception->getMessage()
        );

        $exception = new InvalidKeyGenericException(stdClass::class);
        $this->assertSame(
            'Type of `stdClass` is invalid. Expected one of [integer, numeric, string, boolean, NULL] or custom generic type.',
            $exception->getMessage()
        );
    }
}
