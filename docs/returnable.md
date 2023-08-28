# Returnable Typed Collections

Returnable typed collections allow you to return a collection that always
will contain what you expect it to be and helps developers to understand
what needs to be returned.

For example: A Class named Users
should be a Collection that contains an array of User models.

A returnable Collection is easily created by extending the `TypedCollection`
class and implementing the `generics` method. You can also specify the
generics for the key here if you want to, by implementing the `keyGnerics`
method.

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

## Lazy Collections

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

Note: be aware the value and or key type is validated when yielded and
not before or after.

Note: the `keyGenerics` method is optional. When omitted, any type
that's valid as key can be used.

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

## Discarding Invalid Types

Instead of checking if your variable is allowed in the collection, you can
simply tell your typed collection to discard any invalid types. just implement
the `DiscardsInvalidTypes` interface.

````php
use Henzeb\Collection\TypedCollection;
use Henzeb\Collection\Enums\Type;
use Henzeb\Collection\Contracts\DiscardsInvalidTypes;

class PostCollection extends TypedCollection implements DiscardsInvalidTypes
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

// would create an empty collection:
new PostCollection(['invalid']);
// would discard invalid and just add the new Post:
(new PostCollection())->push('invalid', new Post());
// would not prepend the value and return the current Collection instance:
(new PostCollection())->prepend('invalid');
// would not add the value and return the current Collection instance:
(new PostCollection())->add(true);
````

Note: This works with `LazyTypedCollection` as well.
