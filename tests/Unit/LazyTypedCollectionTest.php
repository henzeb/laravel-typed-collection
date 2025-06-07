<?php

use Henzeb\Collection\Contracts\DiscardsInvalidTypes;
use Henzeb\Collection\Enums\Type;
use Henzeb\Collection\Exceptions\InvalidKeyTypeException;
use Henzeb\Collection\Exceptions\InvalidTypeException;
use Henzeb\Collection\Exceptions\MissingGenericsException;
use Henzeb\Collection\Exceptions\MissingKeyGenericsException;
use Henzeb\Collection\Exceptions\MissingTypedCollectionException;
use Henzeb\Collection\Generics\Uuid;
use Henzeb\Collection\Lazy\Jsons;
use Henzeb\Collection\Lazy\Strings;
use Henzeb\Collection\LazyTypedCollection;
use Henzeb\Collection\Support\GenericsLazyCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\LazyCollection;
test('validates invalid generics', function () {
    expect(fn() => new class extends LazyTypedCollection {
        protected function generics(): string|Type|array
        {
            return ['failure'];
        }
    })->toThrow(MissingTypedCollectionException::class);
});

test('validates empty generics', function () {
    expect(fn() => new class extends LazyTypedCollection {
        protected function generics(): string|Type|array
        {
            return [];
        }
    })->toThrow(MissingGenericsException::class);
});

test('validates generics', function () {
    new class extends LazyTypedCollection {
        protected function generics(): string|Type|array
        {
            return Type::String;
        }
    };
    
    expect(true)->toBeTrue();
});

test('does not validate types on new instance', function () {
    new class([$this]) extends LazyTypedCollection {
        protected function generics(): string|Type|array
        {
            return Type::String;
        }
    };
    
    expect(true)->toBeTrue(); // No exception should be thrown
});

test('does validate types when running', function () {
    $lazy = new class([$this]) extends LazyTypedCollection {
        protected function generics(): string|Type|array
        {
            return Type::String;
        }
    };

    expect(fn() => $lazy->each(fn() => true))
        ->toThrow(InvalidTypeException::class);
});

test('does not validate type if not fetched', function () {
    $lazy = new class(['myKey' => 'string', $this]) extends LazyTypedCollection {
        protected function generics(): string|Type|array
        {
            return Type::String;
        }
    };

    foreach ($lazy as $key => $item) {
        expect($key)->toBe('myKey');
        expect($item)->toBe('string');
        break;
    }
});

function getKeyableTestcases(): array
{
    return [
        'int' => [0],
        'numeric' => ['12'],
        'String' => ['hello'],
        'Bool' => [true],
        'Null' => [null],
        'Uuid' => [\Ramsey\Uuid\Uuid::uuid4()->toString(), Uuid::class],
        'mixed-int' => [1, [Type::Int, Type::String]],
        'mixed-string' => ['1', [Type::Int, Type::String]],
        'int-fail' => [0, Type::String, true],
        'numeric-fail' => ['12', Type::String, true],
        'String-fail' => ['hello', Type::Bool, true],
        'Bool-fail' => [true, Type::String, true],
        'Null-fail' => [null, Type::String, true],
        'Uuid-fail' => ['ohoh', Uuid::class, true]
    ];
}

test('key validation', function (mixed $key, Type|string|array|null $generics = null, bool $exception = false) {
    $key = is_null($key) ? (int)$key : $key;

    if ($exception) {
        expect(function() use ($key, $generics) {
            $collection = new GenericsLazyCollection(
                [$key => 'world'],
                Type::String,
                $generics,
            );
            $collection->all();
        })->toThrow(InvalidKeyTypeException::class);
    } else {
        $collection = new GenericsLazyCollection(
            [$key => 'world'],
            Type::String,
            $generics,
        );

        expect($collection->all())->toBe([$key => 'world']);
    }
})->with(getKeyableTestcases());

test('missing key generics', function () {
    expect(fn() => new class extends LazyTypedCollection {
        protected function generics(): string|Type|array
        {
            return Type::String;
        }

        protected function keyGenerics(): string|Type|array
        {
            return [];
        }
    })->toThrow(MissingKeyGenericsException::class);
});

test('allow chunks', function () {
    $collection = new class(['hello', 'world', '!']) extends LazyTypedCollection {
        protected function generics(): Type
        {
            return Type::String;
        }
    };

    $chunks = $collection->chunk(2);

    expect($chunks)->toHaveCount(2);

    foreach ($chunks as $chunk) {
        expect($chunk)->toBeInstanceOf($collection::class);
    }

    expect($chunks->first()->all())->toBe(['hello', 'world']);
    expect($chunks->last()->all())->toBe([2 => '!']);
});

test('allow chunk while', function () {
    $collection = new class(['hello', 'world', '!']) extends LazyTypedCollection {
        protected function generics(): Type
        {
            return Type::String;
        }
    };

    $chunks = $collection->chunkWhile(fn() => false)->all();

    expect($chunks)->toHaveCount(3);

    foreach ($chunks as $chunk) {
        expect($chunk)->toBeInstanceOf($collection::class);
    }

    expect($chunks[0]->all())->toBe(['hello']);
    expect($chunks[1]->all())->toBe([1 => 'world']);
    expect($chunks[2]->all())->toBe([2 => '!']);
});

test('allow mapping', function () {
    expect(Strings::make(['string', 'another'])->map(fn(string $string) => $string === 'string')->toArray())
        ->toBe([true, false]);
});

test('allow with keys', function () {
    expect(Strings::make(['string', 'another'])->mapWithKeys(
        fn(string $string, int $key) => [$key + 1 => $string === 'string']
    )->toArray())->toBe([1 => true, 2 => false]);
});

test('keys', function () {
    expect(Strings::make([1 => 'string', 2 => 'another'])->keys()->toArray())
        ->toBe([1, 2]);
});

test('allow map to dictionary', function () {
    $collection = new class([
        [
            'name' => 'John Doe',
            'department' => 'Sales',
        ],
        [
            'name' => 'Jane Doe',
            'department' => 'Sales',
        ],
        [
            'name' => 'Johnny Doe',
            'department' => 'Marketing',
        ]
    ]) extends LazyTypedCollection {
        protected function generics(): Type
        {
            return Type::Array;
        }

        protected function keyGenerics(): string|Type|array
        {
            return Type::Int;
        }
    };

    expect($collection->mapToDictionary(
        function (array $item) {
            return [$item['department'] => $item['name']];
        }
    )->toArray())->toBe([
        'Sales' => [
            'John Doe',
            'Jane Doe'
        ],
        'Marketing' => [
            'Johnny Doe'
        ]
    ]);
});

test('casting', function () {
    $collection = Jsons::wrap([['regular' => 'array']]);

    expect($collection->get(0))->toBe(json_encode(['regular' => 'array']));

    $collection = new class(['string']) extends LazyTypedCollection {
        public function generics(): string|Type|array
        {
            return Type::class;
        }
    };

    expect($collection->first())->toBe(Type::String);

    expect(fn() => (new class(['doesNotExist']) extends LazyTypedCollection {
        public function generics(): string|Type|array
        {
            return Type::class;
        }
    })->first())->toThrow(InvalidTypeException::class);
});


test('discards invalid types', function () {
    $collection = new class(['test', ['test']]) extends LazyTypedCollection implements DiscardsInvalidTypes {
        protected function generics(): string|Type|array
        {
            return Type::Array;
        }
    };

    expect($collection)->toHaveCount(1);
    expect($collection->first())->toBe(['test']);
});

test('to base', function () {
    $collection = new class extends LazyTypedCollection {
        protected function generics(): string|Type|array
        {
            return Type::String;
        }
    };

    expect($collection->toBase())->toBeInstanceOf(LazyCollection::class);

    $collection = new class extends LazyTypedCollection {
        protected function generics(): string|Type|array
        {
            return Type::String;
        }
    };

    expect($collection->toBase())->toBeInstanceOf(LazyCollection::class);

    $collection = new class extends LazyTypedCollection {
        protected function generics(): string|Type|array
        {
            return Type::String;
        }

        protected function baseClass(): string
        {
            return Collection::class;
        }
    };

    expect($collection->toBase())->toBeInstanceOf(Collection::class);
});
