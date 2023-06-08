<?php

namespace Henzeb\Collection\Tests\Unit\Enums;

use Henzeb\Collection\Enums\Type;
use PHPUnit\Framework\TestCase;
use stdClass;

class TypeTest extends TestCase
{
    public static function providesTryFromTestcases(): array
    {
        $result = [
            'invalid' => [null, 'failure']
        ];

        foreach (Type::cases() as $case) {
            $name = strtolower($case->name);
            $result[$name] = [$case, $case->name];
            $result[$name . '-upper'] = [$case, strtoupper($case->name)];
            $result[$name . '-lower'] = [$case, $name];
        }

        return $result;
    }

    /**
     * @param Type|null $expects
     * @param $tryFrom
     * @return void
     *
     * @dataProvider providesTryFromTestcases
     */
    public function testTryFrom(?Type $expects, $tryFrom)
    {
        $this->assertEquals($expects, Type::tryFrom($tryFrom));
    }

    public static function providesFromValueTestcases(): array
    {
        return [
            'bool' => [Type::Bool, true],
            'string' => [Type::String, 'Hello World'],
            'numeric' => [Type::Numeric, '1.0'],
            'integer' => [Type::Int, 1],
            'double' => [Type::Double, 1.1],
            'array' => [Type::Array, ['Array']],
            'null' => [Type::Null, null],
            'resource' => [Type::Resource, fopen('php://memory', 'r+')],
            'closed-resource' => [null, tap(fopen('php://memory', 'r+'), fn($stdin) => fclose($stdin))],
            'object' => [Type::Object, new stdClass()],
        ];
    }

    /**
     * @param Type $expects
     * @param mixed $from
     * @return void
     *
     * @dataProvider providesFromValueTestcases
     */
    public function testFromValue(?Type $expects, mixed $from)
    {
        $this->assertEquals(
            $expects,
            Type::fromValue($from)
        );
    }

    public static function providesEqualsTestcases(): array
    {
        return [
            [null, Type::String, false],
            [Type::Int, Type::String, false],
            [Type::String, Type::String, true],
            [Type::Int, Type::Numeric, true], // an integer is a numeric
            [Type::Double, Type::Numeric, true], // a double is a numeric,
            [Type::Numeric, Type::Numeric, true], // a numeric is a numeric,
            [Type::Numeric, Type::Int, false], // a numeric value is not per sé an integer
            [Type::Numeric, Type::Double, false], // a numeric value is not per sé a double
        ];
    }

    /**
     * @param Type|null $match
     * @param Type $with
     * @param bool $expected
     * @return void
     *
     * @dataProvider providesEqualsTestcases
     */
    public function testEquals(Type|null $match, Type $with, bool $expected)
    {
        $this->assertEquals($expected, $with->equals($match));
    }

    public static function providesValueTestcases()
    {
        return [
            ['type' => Type::Bool, 'expected' => 'boolean'],
            ['type' => Type::String, 'expected' => 'string'],
            ['type' => Type::Int, 'expected' => 'integer'],
            ['type' => Type::Double, 'expected' => 'double'],
            ['type' => Type::Array, 'expected' => 'array'],
            ['type' => Type::Null, 'expected' => 'NULL'],
            ['type' => Type::Resource, 'expected' => 'resource'],
            ['type' => Type::Object, 'expected' => 'object'],
            ['type' => Type::Numeric, 'expected' => 'numeric']
        ];
    }

    /**
     * @param Type $type
     * @param string $expected
     * @return void
     * @dataProvider providesValueTestcases
     */
    public function testValue(Type $type, string $expected): void
    {
        $this->assertEquals($expected, $type->value());
    }
}
