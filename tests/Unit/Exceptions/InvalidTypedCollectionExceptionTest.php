<?php

namespace Henzeb\Collection\Tests\Unit\Exceptions;

use Henzeb\Collection\Exceptions\InvalidTypedCollectionException;
use PHPUnit\Framework\TestCase;

class InvalidTypedCollectionExceptionTest extends TestCase
{
    public function testMessage()
    {
        $exception = new InvalidTypedCollectionException(self::class);
        $this->assertSame(
            'Invalid typed collection for `InvalidTypedCollectionExceptionTest`. Specify a typed collection in $typedCollection, or remove trait.',
            $exception->getMessage()
        );

        $exception = new InvalidTypedCollectionException('hello world');
        $this->assertSame(
            'Invalid typed collection for `hello world`. Specify a typed collection in $typedCollection, or remove trait.',
            $exception->getMessage()
        );
    }
}
