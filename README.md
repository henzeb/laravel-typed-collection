# Laravel Typed Collection

[![Build Status](https://github.com/henzeb/laravel-typed-collection/workflows/tests/badge.svg)](https://github.com/henzeb/laravel-typed-collection/actions)
[![Latest Version on Packagist](https://img.shields.io/packagist/v/henzeb/laravel-typed-collection.svg?style=flat-square)](https://packagist.org/packages/henzeb/laravel-typed-collection)
[![Total Downloads](https://img.shields.io/packagist/dt/henzeb/laravel-typed-collection.svg?style=flat-square)](https://packagist.org/packages/henzeb/laravel-typed-collection)
[![Test Coverage](https://api.codeclimate.com/v1/badges/b33a1948230c629a3c54/test_coverage)](https://codeclimate.com/github/henzeb/laravel-typed-collection/test_coverage)
[![License](https://img.shields.io/packagist/l/henzeb/laravel-typed-collection)](https://packagist.org/packages/henzeb/laravel-typed-collection)

PHP has no support for Generics. Yet sometimes, we want to be sure
we receive an array of a certain type.

Using Laravel's Collections, we should be able to force types. This
package allows you to.

## Installation

Just install with the following command.

```bash
composer require henzeb/laravel-typed-collection
```

## usage

````php
use Henzeb\Collection\Enums\Type;

$collection = collect()
    ->withGenerics(Type::String, Post::class);

$collection->add('Hello World'); // succeeds
$collection->put('post',new Post()); // succeeds
$collection[] = new User(); // throws InvalidTypeException
````

### key generics

You can also setup generic types for the keys.

````php
use Henzeb\Collection\Enums\Type;
use Henzeb\Collection\Generics\Uuid;

collect()->withKeyGenerics(Type::String);
collect()->withKeyGenerics(Uuid::class);

collect()
    ->withKeyGenerics(Uuid::class)
    ->withGenerics(User::class);
````

### The accepts method

When a type is added to a typed collection that is not accepted,
An exception is thrown. With `accepts`, you can test your value
before adding it to the collection.

````php

$collection = collect()
    ->withGenerics(Type::String);

$value = 'Hello World';
if($collection->accepts($value)) {
    // accepted
}

$value = true;
if($collection->accepts($value)) {
    // not accepted
}

````

### The acceptsKey method

The same works for the key:

````php

$collection = collect()
    ->withKeyGenerics(Type::String);

$key = 'Hello World';
if($collection->acceptsKey($key)) {
    // accepted
}

$key = true;
if($collection->acceptsKey($key)) {
    // not accepted
}
 ````

### Filtering a collection

with `onlyGenerics` and `onlyKeyGenerics` you can filter out any value that doesn´t
match the generic type. this can be handy when you want to pipe
the results into a `TypedCollection`.

````php
use Henzeb\Collection\Enums\Type;

$collection = collect(['Hello world', new Post()])
    ->onlyGenerics(Type::String);

$collection->all(); // returns ['Hello world'];

$collection = collect(['Hello world', true, new User()])
    ->lazy()
    ->onlyGenerics(Type::String, Type::Bool);

$collection->all(); // returns ['Hello world', true];
````

````php
use Henzeb\Collection\Enums\Type;
use Henzeb\Collection\Generics\Uuid;

$collection = collect([ 1=>'Hello', 'world' => 'world'])
    ->onlyKeyGenerics(Type::Int);

$collection->all(); // returns ['Hello'];

$collection = collect([ 1 => 'Hello', 'baf87839-9d8e-4bfd-a77b-2f51cd8529c9' => 'world', 'string'=>'!'])
    ->lazy()
    ->onlyKeyGenerics(Uuid::class, Type::Int);

$collection->all(); // returns [1 => 'Hello', 'baf87839-9d8e-4bfd-a77b-2f51cd8529c9' => 'world'];
````

### Extending TypedCollection

````php
use Henzeb\Collection\TypedCollection;
use Henzeb\Collection\Enums\Type;
use Henzeb\Collection\Generics\Uuid;

class PostCollection extends TypedCollection
{
    protected function generics(): string|Type|array
    {
        return Post::class;
    }
}

class MixedCollection extends TypedCollection
{
    protected function generics(): string|Type|array
    {
        return [Post::class, Type::String];
    }
}

class PostCollection extends TypedCollection
{
    protected function generics(): string|Type|array
    {
        return Post::class;
    }
    
    protected function keyGenerics() : string|Type|array 
    {
        return Uuid::class;   
    }
}

class MixedCollectionWithIntegerKeys extends TypedCollection
{
    protected function generics(): string|Type|array
    {
        return Type::Mixed;
    }
    
    protected function keyGenerics() : string|Type|array 
    {
        return Type::Int;   
    }
}

class MixedCollectionWithIntegerKeys extends TypedCollection
{
    protected function generics(): string|Type|array
    {
        return Type::Mixed;
    }
    
    protected function keyGenerics() : string|Type|array 
    {
        return [Type::Int, Uuid::class];   
    }
}
````

### Lazy Collections

````php
use Henzeb\Collection\LazyTypedCollection;
use Henzeb\Collection\Enums\Type;

class PostCollection extends LazyTypedCollection
{
    protected function generics(): string|Type|array
    {
        return Post::class;
    }
}

class MixedCollection extends LazyTypedCollection
{
    protected function generics(): string|Type|array
    {
        return [Post::class, Type::String];
    }
}

class PostCollection extends LazyTypedCollection
{
    protected function generics(): string|Type|array
    {
        return Post::class;
    }
    
    protected function keyGenerics() : string|Type|array 
    {
        return Uuid::class;   
    }
}

class MixedCollectionWithIntegerKeys extends LazyTypedCollection
{
    protected function generics(): string|Type|array
    {
        return Type::Mixed;
    }
    
    protected function keyGenerics() : string|Type|array 
    {
        return Uuid::class;   
    }
}
````

Note: be aware the value and or key type is validated when yielded and not before or after.

Note: the `keyGenerics` method is optional. When omitted, any type that's valid as key can be used.

### Get Lazy Typed Collection from Typed Collection

Using the `lazy()` method you will receive the default `LazyCollection`,
If that's not what you want, you can specify the `lazyClass`.

````php
class PostCollection extends TypedCollection
{
    protected function generics(): string|Type|array
    {
        return Post::class;
    }

    protected function lazyClass(): string
    {
        return LazyPostCollection::class;
    }
}
````

That way, you will always receive a lazy typed collection.

### Available types and collections

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

Note: Each available collection also has a lazy counterpart.
For `Type::Bool` for example this would be
`Henzeb\Collection\Lazy\Boolean`

#### Custom Generic Types

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

````php
use Henzeb\Collection\TypedCollection;
use Henzeb\Collection\Enums\Type;

class PostCollection extends TypedCollection
{
    protected function generics(): string|Type|array
    {
        return Post::class;
    }

    protected function lazyClass(): string
    {
        return LazyPostCollection::class;
    }
}

(new PostCollection())->lazy(); // now returns a LazyPostCollection instance
````

## Testing this package

```bash
composer test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email
henzeberkheij@gmail.com instead of using the issue tracker.

## Credits

- [Henze Berkheij](https://github.com/henzeb)

## License

The GNU AGPLv. Please see [License File](LICENSE.md) for more information.
