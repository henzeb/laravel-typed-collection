<?php

namespace Henzeb\Collection\Tests\Stubs\Eloquent;

use Henzeb\Collection\Concerns\TypedCollection;
use Illuminate\Database\Eloquent\Model;
use Sushi\Sushi;

/**
 * @property integer $id
 * @property string $name
 * @property string $last_name
 */
class User extends Model
{
    use Sushi, TypedCollection;

    protected $guarded = [];

    private string $typedCollection = Users::class;

    public array $rows = [
        ['id' => 1, 'name' => 'Joe', 'last_name' => 'West'],
        ['id' => 2, 'name' => 'Wally', 'last_name' => 'West'],
        ['id' => 3, 'name' => 'Iris', 'last_name' => 'Allen-West'],
    ];
}
