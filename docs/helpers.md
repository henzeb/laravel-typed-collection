# Helper Methods

## The accepts method

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

## The acceptsKey method

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

## Filtering a collection

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

$collection = collect(
    [
        1 => 'Hello',
        'baf87839-9d8e-4bfd-a77b-2f51cd8529c9' => 'world',
        'string'=>'!'
    ])->lazy()
    ->onlyKeyGenerics(Uuid::class, Type::Int);

// returns [1 => 'Hello', 'baf87839-9d8e-4bfd-a77b-2f51cd8529c9' => 'world'];
$collection->all();
````
