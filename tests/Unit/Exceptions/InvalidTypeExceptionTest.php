<?php

namespace Henzeb\Collection\Tests\Unit\Exceptions;

use Henzeb\Collection\Enums\Type;
use Henzeb\Collection\Exceptions\InvalidTypeException;
use PHPUnit\Framework\TestCase;

class InvalidTypeExceptionTest extends TestCase
{
    public function testMessageContainsGeneric()
    {
        $exception = new InvalidTypeException(
            self::class,
            'Hello World',
            [self::class, Type::Numeric]
        );
        $this->assertSame(
            'Henzeb\Collection\Tests\Unit\Exceptions\InvalidTypeExceptionTest: The given value `string` does not match (one of) the generic type [Henzeb\Collection\Tests\Unit\Exceptions\InvalidTypeExceptionTest, numeric] for this collection.',
            $exception->getMessage()
        );
    }

    public function testMessageContainsUnknownGeneric()
    {
        $exception = new InvalidTypeException(
            self::class,
            'Hello World',
            [$this]
        );
        $this->assertSame(
            'Henzeb\Collection\Tests\Unit\Exceptions\InvalidTypeExceptionTest: The given value `string` does not match (one of) the generic type [unknown] for this collection.',
            $exception->getMessage()
        );
    }

    public static function providesTypeTestcases()
    {
        return [
            'string' => ['item' => 'Hello World', 'string'],
            'numeric' => ['item' => '21', 'numeric'],
            'resource' => ['item' => STDIN, 'resource'],
            'double' => ['item' => 1.1, 'double'],
            'array' => ['item' => ['hello'], 'array'],
            'boolean' => ['item' => true, 'boolean'],
            'integer' => ['item' => 21, 'integer'],
        ];
    }

    /**
     * @return void
     *
     * @dataProvider providesTypeTestcases
     */
    public function testMessageContainsGenericTypeOfItem(mixed $item, string $expectedValue)
    {
        $exception = new InvalidTypeException(
            'test',
            $item,
            []
        );

        $this->assertSame(
            'test: The given value `' . $expectedValue . '` does not match (one of) the generic type [] for this collection.',
            $exception->getMessage()
        );
    }
}
