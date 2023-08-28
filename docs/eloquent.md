# Typed Eloquent Collections

With this package, you can directly return a typed collection
directly with Laravel Eloquent.

````php
use Henzeb\Collection\Concerns\TypedCollection;
use Illuminate\Database\Eloquent\Model;

class User extends Model {

    use TypedCollection;

    protected string $typedCollection = Users::class;
}
````

You can use typed collections and lazy typed collections here. if
`$typedCollection` is null or omitted, Exceptions will be thrown.

If you need to use features from the Eloquent Collection, there is
a Typed Collection class for that too:

````php
use Henzeb\Collection\EloquentTypedCollection;

class Users extends EloquentTypedCollection
{
    protected function generics(): string
    {
        return User::class;
    }
}
````

## The baseClass method

When using `EloquentTypedCollections`, you may want to override
the base class to allow you to return a 'regular' typed collection.
You can do so with the `baseClass` method.

````php
namespace Your\Project\Eloquent;

use Henzeb\Collection\EloquentTypedCollection;
use Your\Project\Typed\Users as TypedUsers;

class Users extends EloquentTypedCollection
{
    protected function generics(): string
    {
        return User::class;
    }

    protected function baseClass() : string
    {
         return TypedUsers::class;
    }
}
````

Like with `lazyClass`, when omitted, it will use the default behavior.

