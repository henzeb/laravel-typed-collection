<?php

namespace Henzeb\Collection\Tests\Unit\Generics;

use Henzeb\Collection\Generics\Uuid;
use PHPUnit\Framework\TestCase;

class UuidTest extends TestCase
{
    public function testUuidValidates(): void
    {
        $this->assertTrue(
            Uuid::matchesType(
                '1b2a5269-de6f-4e19-a75a-06c0262dbcd7'
            )
        );
    }

    public function testFailsOnNonString(): void
    {
        $this->assertFalse(
            Uuid::matchesType(
                $this
            )
        );
    }

    public function testFailsOnNotAJsonString(): void
    {
        $this->assertFalse(Uuid::matchesType('Not a Uuid'));
    }

    public function testFailsOnInvalidUuid(): void
    {
        $this->assertFalse(
            Uuid::matchesType(
                '1b2a5269-de6f-4e19-a75a-06c0262dbcd'
            )
        );
    }
}
