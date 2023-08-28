<?php

namespace Henzeb\Collection\Tests\Unit;

use Henzeb\Collection\EloquentTypedCollection;
use Henzeb\Collection\Enums\Type;
use Henzeb\Collection\Tests\Stubs\Eloquent\User;
use Henzeb\Collection\Tests\Stubs\Eloquent\Users;
use Henzeb\Collection\Typed\Arrays;
use Illuminate\Support\Facades\File;
use Orchestra\Testbench\TestCase;

class EloquentTypedCollectionTest extends TestCase
{
    public function testMapWithKeys(): void
    {
        $collection = User::all()->mapWithKeys(
            function (User $user) {
                return [
                    $user->last_name . '.' . $user->id => $user->name,
                ];
            }
        )->undot();

        $this->assertEquals(
            $collection->toArray(),
            [
                'West' => [
                    1 => 'Joe',
                    2 => 'Wally'
                ],
                'Allen-West' => [
                    3 => 'Iris'
                ]
            ]
        );
    }

    public function testMerge()
    {
        $user = new User(['id' => 4, 'name' => 'Barry', 'last_name' => 'Allen']);
        $collection = User::all()->merge(
            [
                $user
            ]
        );

        $this->assertInstanceOf(Users::class, $collection);

        $this->assertCount(4, $collection);

        $this->assertEquals($user, $collection->get(3));
    }

    public function testRefresh()
    {
        $collection = User::all();
        $user = User::find(3);
        $user->last_name = 'West';
        $user->save();

        $this->assertEquals('Allen-West', $collection->get(2)->last_name);

        $collection = $collection->fresh();

        $this->assertEquals('West', $collection->get(2)->last_name);
    }

    public function testToBase()
    {
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

        $this->assertInstanceOf(Arrays::class, $collection->toBase());

        $mapped = $collection->map(fn(array $array) => json_encode($array))->toArray();

        $this->assertEquals([json_encode(['regular' => 'array'])], $mapped);
    }

    protected function tearDown(): void
    {
        File::deleteDirectory(storage_path('framework/cache'));
        parent::tearDown();
    }
}
