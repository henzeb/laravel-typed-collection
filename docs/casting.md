# Casting

There might be cases you want to be able to cast certain types
into other types. For example: Arrays can be cast into JSON.

In order to allow for casting, you implement the `CastableGenericType`
interface.

````php
use Henzeb\Collection\Contracts\CastableGenericType;

class JSON implements CastableGenericType
{
    public static function castType(mixed $item): mixed
    {
        if(is_array($item)) {
            return json_encode($item);
        }

        return $item;
    }

    public static function matchesType(mixed $item): mixed
    {
        // ...
    }
}
````

As you can see, I test if the given item is an array. If it is,
I return a json encoded string. Else I return the item as is.

Note: You must return the item as is if you cannot cast it, otherwise
you'll be casting it to `null`.

Note: The `Json` Generic Type in this package already implement's
`CastableGenericType` and allows casting for arrays, collections
and other iterable and jsonable objects.

## Using Typecasting on regular classes

`TypeCast` is not limited to GenericTypes. This means you can cast
for example arrays into the object you're working in.

````php
use Henzeb\Collection\Contracts\CastableGenericType;

class User implements CastableGenericType
{
    public function __construct(
        private int $id,
        private string $name,
        private string $lastName
    ) {}

    public static function castType(mixed $item): mixed
    {
        if(is_array($item)) {
            return new self(
                $item['id'],
                $item['name'],
                $itemp['lastname'],
            )
        }

        return $item;
    }

    public static function matchesType(mixed $item): mixed
    {
        // ...
    }
}
````

## Enums

Enums are automatically cast. Under the hood it uses `tryFrom`. There
are no checks in place that checks if it's a `BackedEnum`, so you can use
packages like [henzeb/enumhancer](https://github.com/henzeb/enumhancer) or
your own custom `tryFrom` method.

