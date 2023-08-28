# Generic Typed Collections

Generic Typed Collections are the simplest form of Typed Collections.
They allow you to enforce one or more types.

````php
use Henzeb\Collection\Enums\Type;

$collection = collect()
    ->withGenerics(Type::String, Post::class);

$collection->add('Hello World'); // succeeds
$collection->put('post',new Post()); // succeeds
$collection[] = new User(); // throws InvalidTypeException
````

## key generics

You can also set up generic types for the keys.

````php
use Henzeb\Collection\Enums\Type;
use Henzeb\Collection\Generics\Uuid;

collect()->withKeyGenerics(Type::String);

collect()->withKeyGenerics(Uuid::class);

collect()
    ->withKeyGenerics(Uuid::class)
    ->withGenerics(User::class);
````
