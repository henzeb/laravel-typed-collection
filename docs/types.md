# Available types and collections

There is a `Type` enum which supports all types supported by PHP.
Next to `Type`, you can add in Fully Qualified Class Names of interfaces
or objects.

| Generic Type   | Collection                        |
|----------------|-----------------------------------|
| Type::Bool     | Henzeb\Collection\Typed\Booleans  |
| Type::String   | Henzeb\Collection\Typed\Strings   |
| Type::Int      | Henzeb\Collection\Typed\Integers  |
| Type::Double   | Henzeb\Collection\Typed\Doubles   |
| Type::Numeric  | Henzeb\Collection\Typed\Numerics  |
| Type::Array    | Henzeb\Collection\Typed\Arrays    |
| Type::Null     | -                                 |
| Type::Resource | Henzeb\Collection\Typed\Resources |
| Type::Object   | Henzeb\Collection\Typed\Objects   |
| Type::Mixed    | -                                 |
| JSON           | Henzeb\Collection\Typed\Jsons     |
| Uuid           | Henzeb\Collection\Typed\Uuid      |
| Ulid           | Henzeb\Collection\Typed\Ulid      |

Note: Each available collection also has a lazy counterpart.
For `Type::Bool` for example this would be
`Henzeb\Collection\Lazy\Boolean`

## Custom Generic Types

Sometimes, you want to validate scalar types some more. For example `JSON`.
To achieve that, you can use the `GenericType` interface.

````php
use Henzeb\Collection\Contracts\GenericType;

readonly class Json implements GenericType
{
    public static function matchesType(mixed $item): bool
    {
        /** json_validate is a poly-fill function ahead of php 8.3 */
        return is_string($item) && json_validate($item);
    }
}
````

And then use it as such:

````php
use Henzeb\Collection\TypedCollection;
use Henzeb\Collection\Enums\Type;

class JsonCollection extends TypedCollection
{
    protected function generics() : string|Type|array
    {
        return JSON::class;
    }
}

(new JsonCollection())->add('{"hello":"world"}'); // succeeds
(new JsonCollection())->add('{"hello":"world"'); // throws InvalidTypeException
(new JsonCollection())->add(['hello'=>'world']); // throws InvalidTypeException

````
