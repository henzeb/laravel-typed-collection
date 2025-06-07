<?php

use Henzeb\Collection\EloquentTypedCollection;
use Henzeb\Collection\Enums\Type;
use Henzeb\Collection\Tests\Stubs\Eloquent\User;
use Henzeb\Collection\Tests\Stubs\Eloquent\Users;
use Henzeb\Collection\Typed\Arrays;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;

afterEach(function () {
    File::deleteDirectory(storage_path('framework/cache'));
});

test('map with keys', function () {
    $collection = User::all()->mapWithKeys(
        function (User $user) {
            return [
                $user->last_name . '.' . $user->id => $user->name,
            ];
        }
    )->undot();

    expect($collection->toArray())->toEqual([
        'West' => [
            1 => 'Joe',
            2 => 'Wally'
        ],
        'Allen-West' => [
            3 => 'Iris'
        ]
    ]);
});

test('merge', function () {
    $user = new User(['id' => 4, 'name' => 'Barry', 'last_name' => 'Allen']);
    $collection = User::all()->merge([
        $user
    ]);

    expect($collection)->toBeInstanceOf(Users::class);
    expect($collection)->toHaveCount(4);
    expect($collection->get(3))->toEqual($user);
});

test('refresh', function () {
    $collection = User::all();
    $user = User::find(3);
    $user->last_name = 'West';
    $user->save();

    expect($collection->get(2)->last_name)->toBe('Allen-West');

    $collection = $collection->fresh();

    expect($collection->get(2)->last_name)->toBe('West');
});

test('to base', function () {
    $collection = new class extends EloquentTypedCollection {
        protected function generics(): string|Type|array
        {
            return Type::Array;
        }
    };

    expect($collection->toBase())->toBeInstanceOf(Collection::class);
});

test('to base with given class', function () {
    $collection = new class([['regular' => 'array']]) extends EloquentTypedCollection {
        protected function generics(): string|Type|array
        {
            return Type::Array;
        }

        protected function baseClass(): string
        {
            return Arrays::class;
        }
    };

    expect($collection->toBase())->toBeInstanceOf(Arrays::class);

    $mapped = $collection->map(fn(array $array) => json_encode($array))->toArray();

    expect($mapped)->toEqual([json_encode(['regular' => 'array'])]);
});
