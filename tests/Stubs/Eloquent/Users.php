<?php

namespace Henzeb\Collection\Tests\Stubs\Eloquent;

use Henzeb\Collection\EloquentTypedCollection;
use Henzeb\Collection\Enums\Type;

/**
 * @extends EloquentTypedCollection<int, User>
 */
class Users extends EloquentTypedCollection
{
    protected function generics(): string|Type|array
    {
        return User::class;
    }
}
