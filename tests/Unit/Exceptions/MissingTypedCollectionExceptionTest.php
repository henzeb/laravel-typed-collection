<?php

namespace Henzeb\Collection\Tests\Unit\Exceptions;

use Henzeb\Collection\Exceptions\MissingTypedCollectionException;
use PHPUnit\Framework\TestCase;

class MissingTypedCollectionExceptionTest extends TestCase
{
    public function testMessage()
    {
        $exception = new MissingTypedCollectionException(self::class);
        $this->assertSame(
            'Missing typed collection for `MissingTypedCollectionExceptionTest`. Specify one in $typedCollection, or remove trait.',
            $exception->getMessage()
        );

        $exception = new MissingTypedCollectionException('another planet');
        $this->assertSame(
            'Missing typed collection for `another planet`. Specify one in $typedCollection, or remove trait.',
            $exception->getMessage()
        );
    }
}
