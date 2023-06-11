<?php

namespace Henzeb\Collection\Tests\Unit\Exceptions;

use Henzeb\Collection\Exceptions\MissingKeyGenericsException;
use PHPUnit\Framework\TestCase;

class MissingKeyGenericsExceptionTest extends TestCase
{
    public function testMessageContainsGeneric()
    {
        $exception = new MissingKeyGenericsException();
        $this->assertSame(
            'At least one generic key type must be specified.',
            $exception->getMessage()
        );
    }
}
