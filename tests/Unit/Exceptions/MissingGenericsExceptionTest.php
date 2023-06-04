<?php

namespace Henzeb\Collection\Tests\Unit\Exceptions;

use Henzeb\Collection\Exceptions\MissingGenericsException;
use PHPUnit\Framework\TestCase;

class MissingGenericsExceptionTest extends TestCase
{
    public function testMessageContainsGeneric()
    {
        $exception = new MissingGenericsException();
        $this->assertSame(
            'At least one generic type must be specified.',
            $exception->getMessage()
        );
    }
}
