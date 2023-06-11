<?php

namespace Henzeb\Collection\Enums;

enum Type
{
    case Bool;
    case String;
    case Int;
    case Double;
    case Numeric;
    case Array;
    case Null;
    case Resource;
    case Object;
    case Mixed;

    public static function tryFrom(string $name): ?self
    {
        $name = strtolower($name);

        foreach (self::cases() as $case) {
            if (strtolower($case->name) === $name) {
                return $case;
            }
        }

        return null;
    }

    public static function fromValue(mixed $value): ?self
    {
        if (is_string($value) && is_numeric($value)) {
            return self::Numeric;
        }

        return match (gettype($value)) {
            'boolean' => self::Bool,
            'string' => self::String,
            'integer' => self::Int,
            'double' => self::Double,
            'array' => self::Array,
            'NULL' => self::Null,
            'resource' => self::Resource,
            'object' => self::Object,
            default => null
        };
    }

    public static function keyables(): array
    {
        return [
            Type::Int,
            Type::Numeric,
            Type::String,
            Type::Bool,
            Type::Null,
        ];
    }

    public function keyable(): bool
    {
        return in_array($this, self::keyables());
    }

    public function equals(?Type $type): bool
    {
        if ($this === self::Numeric
            && in_array($type, [self::Int, self::Double, self::Numeric])
        ) {
            return true;
        }

        if ($type === $this) {
            return true;
        }

        return false;
    }

    public function value(): string
    {
        return match ($this) {
            self::Bool => 'boolean',
            self::String => 'string',
            self::Int => 'integer',
            self::Double => 'double',
            self::Array => 'array',
            self::Null => 'NULL',
            self::Resource => 'resource',
            self::Object => 'object',
            self::Numeric => 'numeric'
        };
    }
}
