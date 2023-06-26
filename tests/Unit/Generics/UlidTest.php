<?php

namespace Henzeb\Collection\Tests\Unit\Generics;

use Henzeb\Collection\Generics\Ulid;
use PHPUnit\Framework\TestCase;

class UlidTest extends TestCase
{
    public function testUlidValidates(): void
    {
        $this->assertTrue(
            Ulid::matchesType(
                '01H3EJ0BZ590C9N423E96YS2NS'
            )
        );
    }

    public function testFailsOnNonString(): void
    {
        $this->assertFalse(
            Ulid::matchesType(
                $this
            )
        );
    }

    public function testFailsOnNotUlidString(): void
    {
        $this->assertFalse(Ulid::matchesType('Not a Uuid'));
    }

    public function testFailsOnInvalidUlid(): void
    {
        $this->assertFalse(
            Ulid::matchesType(
                '01H3EJ0BZ590C9N423E96YS2N'
            )
        );
    }
}
