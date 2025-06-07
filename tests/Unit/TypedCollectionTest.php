<?php

use Henzeb\Collection\Contracts\CastableGenericType;
use Henzeb\Collection\Contracts\DiscardsInvalidTypes;
use Henzeb\Collection\Contracts\GenericType;
use Henzeb\Collection\Enums\Type;
use Henzeb\Collection\Exceptions\InvalidKeyGenericException;
use Henzeb\Collection\Exceptions\InvalidKeyTypeException;
use Henzeb\Collection\Exceptions\InvalidTypeException;
use Henzeb\Collection\Exceptions\MissingGenericsException;
use Henzeb\Collection\Exceptions\MissingKeyGenericsException;
use Henzeb\Collection\Exceptions\MissingTypedCollectionException;
use Henzeb\Collection\Generics\Json;
use Henzeb\Collection\Generics\Uuid;
use Henzeb\Collection\LazyTypedCollection;
use Henzeb\Collection\Support\GenericsCollection;
use Henzeb\Collection\Typed\Jsons;
use Henzeb\Collection\Typed\Strings;
use Henzeb\Collection\TypedCollection;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Support\LazyCollection;
use Henzeb\Collection\Tests\Stubs\TestObject;

test('empty generics', function () {
    expect(fn() => new class([]) extends TypedCollection {
        protected function generics(): string|Type|array
        {
            return [];
        }
    })->toThrow(MissingGenericsException::class);
});

test('invalid generics', function () {
    expect(fn() => new class([]) extends TypedCollection {
        protected function generics(): string|Type|array
        {
            return 'RandomDoesNotExistString';
        }
    })->toThrow(MissingTypedCollectionException::class);
});

test('invalid generics with object', function () {
    try {
        new class([]) extends TypedCollection {
            protected function generics(): string|Type|array
            {
                return [$this];
            }
        };
        expect(false)->toBeTrue();
    } catch (MissingTypedCollectionException $e) {
        expect($e->getMessage())->toMatch('/`object`/');
    }
});

test('invalid generics where one is valid', function () {
    expect(fn() => new class([]) extends TypedCollection {
        protected function generics(): string|Type|array
        {
            return ['bool', Type::String, 'RandomDoesNotExistString'];
        }
    })->toThrow(MissingTypedCollectionException::class);
});

test('invalid generics where one is invalid', function () {
    expect(fn() => new class([]) extends TypedCollection {
        protected function generics(): string|Type|array
        {
            return [self::class, 'stringed'];
        }
    })->toThrow(MissingTypedCollectionException::class);
});

test('valid generics class', function () {
    new class([]) extends TypedCollection {
        protected function generics(): string|Type|array
        {
            return [self::class];
        }
    };
    
    expect(true)->toBeTrue();
});

test('valid generics type string', function () {
    new class([]) extends TypedCollection {
        protected function generics(): string|Type|array
        {
            return ['bool'];
        }
    };
    
    expect(true)->toBeTrue(); // No exception should be thrown
});

test('valid generics with type', function () {
    new class([]) extends TypedCollection {
        protected function generics(): string|Type|array
        {
            return [Type::Numeric];
        }
    };
    
    expect(true)->toBeTrue(); // No exception should be thrown
});

test('validate items', function () {
    $testObject = new TestObject();
    
    $collection = new class(['Hello World']) extends TypedCollection {
        protected function generics(): string|Type|array
        {
            return ['string'];
        }
    };

    $collection2 = new class(['Hello World', $testObject], $testObject) extends TypedCollection {
        public function __construct($items, private $testObject) {
            parent::__construct($items);
        }
        
        protected function generics(): string|Type|array
        {
            return ['string', TestObject::class];
        }
    };

    expect($collection->all())->toBe(['Hello World']);
    expect($collection2->all())->toBe(['Hello World', $testObject]);
});

test('validate items fail', function () {
    $testObject = new TestObject();
    expect(fn() => new class([$testObject]) extends TypedCollection {
        protected function generics(): string|Type|array
        {
            return [TypedCollection::class];
        }
    })->toThrow(InvalidTypeException::class);
});

test('validate null fail', function () {
    expect(fn() => new class([null]) extends TypedCollection {
        protected function generics(): string|Type|array
        {
            return [TypedCollection::class];
        }
    })->toThrow(InvalidTypeException::class);
});

test('add fail', function () {
    $testObject = new TestObject();
    expect(fn() => (new class() extends TypedCollection {
        protected function generics(): string|Type|array
        {
            return ['string'];
        }
    })->add($testObject))->toThrow(InvalidTypeException::class);
});

test('add success', function () {
    $testObject = new TestObject();
    $collection = (new class() extends TypedCollection {
        protected function generics(): string|Type|array
        {
            return [TestObject::class];
        }
    })->add($testObject);

    expect($collection->all())->toBe([$testObject]);
});

test('push fail', function () {
    $testObject = new TestObject();
    expect(fn() => (new class() extends TypedCollection {
        protected function generics(): string|Type|array
        {
            return ['string'];
        }
    })->push('text', $testObject))->toThrow(InvalidTypeException::class);
});

test('push success', function () {
    $testObject = new TestObject();
    $collection = (new class() extends TypedCollection {
        protected function generics(): string|Type|array
        {
            return [TestObject::class];
        }
    })->push($testObject);

    expect($collection->all())->toBe([$testObject]);
});

test('prepend fail', function () {
    $testObject = new TestObject();
    expect(fn() => (new class() extends TypedCollection {
        protected function generics(): string|Type|array
        {
            return ['string'];
        }
    })->prepend($testObject))->toThrow(InvalidTypeException::class);
});

test('prepend fail on key', function () {
    expect(fn() => (new class() extends TypedCollection {
        protected function generics(): string|Type|array
        {
            return ['string'];
        }

        protected function keyGenerics(): Type
        {
            return Type::Int;
        }
    })->prepend('string', 'anotherstring'))->toThrow(InvalidKeyTypeException::class);
});

test('prepend success', function () {
    $testObject = new TestObject();
    $collection = (new class() extends TypedCollection {
        protected function generics(): string|Type|array
        {
            return [TestObject::class];
        }
    })->prepend($testObject);

    expect($collection->all())->toBe([$testObject]);
});

test('collect', function () {
    $testObject = new TestObject();
    $collection = new class([$testObject], $testObject) extends TypedCollection {
        public function __construct($items, private $testObject) {
            parent::__construct($items);
        }
        
        protected function generics(): string|Type|array
        {
            return [TestObject::class];
        }
    };
    $collectionActual = $collection->collect();
    $collectionActual->add($testObject);
    expect($collectionActual->all())->toBe([$testObject, $testObject]);

    expect($collection)->not->toBe($collectionActual);
});

test('lazy', function () {
    $typed = new class(['hello world']) extends TypedCollection {
        protected function generics(): string|Type|array
        {
            return Type::String;
        }
    };

    $lazy = $typed->lazy();

    expect($lazy::class)->toBe(LazyCollection::class);

    expect($lazy->all())->toBe(['hello world']);
});

test('lazy fails with self class', function () {
    $typed = new class(['hello world']) extends TypedCollection {
        protected function generics(): string|Type|array
        {
            return Type::String;
        }

        protected function lazyClass(): string
        {
            return self::class;
        }
    };

    expect(fn() => $typed->lazy())->toThrow(TypeError::class);
});

test('lazy with custom class', function () {
    $lazyClass = new class() extends LazyTypedCollection {
        protected function generics(): string|Type|array
        {
            return Type::String;
        }
    };

    $typed = new class(['hello world']) extends TypedCollection {
        public string $lazyClass = '';

        protected function generics(): string|Type|array
        {
            return Type::String;
        }

        protected function lazyClass(): string
        {
            return $this->lazyClass;
        }
    };

    $typed->lazyClass = $lazyClass::class;

    $lazy = $typed->lazy();

    expect($lazy::class)->toBe($lazyClass::class);

    expect($lazy->all())->toBe(['hello world']);
});

test('interface as generic', function () {
    $collection = new class extends TypedCollection {
        protected function generics(): string|Type|array
        {
            return ShouldBeUnique::class;
        }
    };

    $collection->add(
        new class implements ShouldBeUnique {

        }
    );
    
    expect(true)->toBeTrue(); // No exception should be thrown
});

test('custom generic type', function () {
    $genericType = new class implements GenericType {
        public static function matchesType(mixed $item): bool
        {
            return true;
        }
    };

    $collection = new class($genericType::class, ['Hello World'],) extends TypedCollection {
        public function __construct(private string $genericType, $items = [])
        {
            parent::__construct($items);
        }

        protected function generics(): string|Type|array
        {
            return $this->genericType;
        }
    };

    expect($collection->all())->toBe(['Hello World']);
});

test('custom generic type fail', function () {
    $genericType = new class implements GenericType {
        public static function matchesType(mixed $item): bool
        {
            return false;
        }
    };

    expect(fn() => new class($genericType::class, ['Hello World']) extends TypedCollection {
        public function __construct(private string $genericType, $items = [])
        {
            parent::__construct($items);
        }

        protected function generics(): string|Type|array
        {
            return $this->genericType;
        }
    })->toThrow(InvalidTypeException::class);
});

test('generic key validation', function () {
    foreach (Type::keyables() + [Uuid::class] as $keyable) {
        new GenericsCollection([], Type::String, $keyable);
    }

    new GenericsCollection(
        [],
        Type::String,
        [Type::String, Uuid::class, Json::class]
    );
    
    expect(true)->toBeTrue(); // No exception should be thrown
});

test('generic key fail after success with type', function () {
    expect(fn() => new GenericsCollection(
        [],
        Type::String,
        [Type::String, TestObject::class, Json::class]
    ))->toThrow(InvalidKeyGenericException::class);
});

test('generic key fail after success with generic type', function () {
    expect(fn() => new GenericsCollection(
        [],
        Type::String,
        [Json::class, 'not a valid type']
    ))->toThrow(InvalidKeyGenericException::class);
});

test('generic key accepts stringed types', function () {
    new GenericsCollection(
        [],
        Type::String,
        ['string', 'int', 'bool']
    );
    
    expect(true)->toBeTrue(); // No exception should be thrown
});

test('invalid generic key string', function () {
    expect(fn() => new GenericsCollection([], Type::String, self::class))
        ->toThrow(InvalidKeyGenericException::class);
});

test('invalid generic key object', function () {
    $collection = new GenericsCollection([], Type::String, Type::String);

    expect(fn() => $collection->put(new stdClass(), 'test'))
        ->toThrow(InvalidKeyTypeException::class);
});

function providesKeyableTestcases(): array
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
            $collection = new GenericsCollection(
                [$key => 'world'],
                Type::String,
                $generics,
            );
            $collection->all();
        })->toThrow(InvalidKeyTypeException::class);
    } else {
        $collection = new GenericsCollection(
            [$key => 'world'],
            Type::String,
            $generics,
        );

        expect($collection->all())->toBe([$key => 'world']);
    }
})->with(providesKeyableTestcases());

test('key validation with string generic type', function () {
    $collection = new GenericsCollection(
        ['test' => 'value'], 
        Type::String,
        'string'
    );
    
    expect($collection->all())->toBe(['test' => 'value']);
    
    expect($collection->acceptsKey('valid_string_key'))->toBeTrue();
    expect($collection->acceptsKey(123))->toBeFalse();
});

test('missing key generics', function () {
    expect(fn() => new class extends TypedCollection {
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

test('push key generics validation', function () {
    $collection = (new GenericsCollection([], Type::String))
        ->push('hello', 'world');

    expect($collection->all())->toBe(['hello', 'world']);

    expect(fn() => (new GenericsCollection([], Type::String, Type::String))
        ->push('hello', 'world'))->toThrow(InvalidKeyTypeException::class);
});

test('allow mixed', function () {
    $expected = ['da', 0, true, 1.1, new stdClass()];
    $collecton = new GenericsCollection(
        $expected,
        Type::Mixed
    );

    expect($collecton->all())->toBe($expected);
});

test('allow chunks', function () {
    $collection = new class(['hello', 'world', '!']) extends TypedCollection {
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
    ]) extends TypedCollection {
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

    $collection->add(['another' => 'array']);

    expect($collection->get(1))->toBe(json_encode(['another' => 'array']));

    $collection->push(['third' => 'array'], ['fourth' => 'array']);
    expect($collection->get(2))->toBe(json_encode(['third' => 'array']));
    expect($collection->get(3))->toBe(json_encode(['fourth' => 'array']));

    $collection->prepend(['fifth' => 'array']);
    expect($collection->get(0))->toBe(json_encode(['fifth' => 'array']));

    $collection = new class extends TypedCollection {
        public function generics(): string|Type|array
        {
            return Type::class;
        }
    };

    $collection->add('string');

    expect($collection->first())->toBe(Type::String);

    $collection->add(Type::String);

    expect(fn() => $collection->add('doesNotExist'))->toThrow(InvalidTypeException::class);
});

test('casting to null', function () {
    $collection = new class extends TypedCollection {
        protected function generics(): string|Type|array
        {
            return (
            new class implements CastableGenericType {
                public static function castType(mixed $item): mixed
                {
                    return null;
                }

                public static function matchesType(mixed $item): bool
                {
                    return $item === null;
                }
            }
            )::class;
        }
    };

    $collection->add('string');
    expect($collection->first())->toBeNull();
});

test('discards invalid types', function () {
    $collection = new class(['test', ['test']]) extends TypedCollection implements DiscardsInvalidTypes {
        protected function generics(): string|Type|array
        {
            return Type::Array;
        }
    };

    expect($collection)->toHaveCount(1);
    expect($collection->first())->toBe(['test']);

    $collection->add(12);
    $collection->add(['hello']);

    $collection->push('test', true, 12, ['12']);

    $collection->prepend('data');

    expect($collection)->toHaveCount(3);
});

test('generics collection with key generics sets generics to mixed', function () {
    $collection = new GenericsCollection(
        ['test' => 'value', 'another' => 456],
        null,
        Type::String
    );
    
    expect($collection->all())->toBe(['test' => 'value', 'another' => 456]);
    expect($collection->acceptsKey('string_key'))->toBeTrue();
    expect($collection->acceptsKey(123))->toBeFalse();
});

test('generics collection lazy class method', function () {
    $collection = new GenericsCollection(['test' => 'value'], Type::String);
    
    $reflection = new ReflectionClass($collection);
    $method = $reflection->getMethod('lazyClass');
    $method->setAccessible(true);
    
    expect($method->invoke($collection))->toBe(\Henzeb\Collection\Support\GenericsLazyCollection::class);
});

test('typed arrays lazy class method', function () {
    $collection = new \Henzeb\Collection\Typed\Arrays([['item1'], ['item2']]);
    
    $reflection = new ReflectionClass($collection);
    $method = $reflection->getMethod('lazyClass');
    $method->setAccessible(true);
    
    expect($method->invoke($collection))->toBe(\Henzeb\Collection\Lazy\Arrays::class);
});

test('typed jsons lazy class method', function () {
    $collection = new \Henzeb\Collection\Typed\Jsons(['{"test": "value"}', '{"another": "json"}']);
    
    $reflection = new ReflectionClass($collection);
    $method = $reflection->getMethod('lazyClass');
    $method->setAccessible(true);
    
    expect($method->invoke($collection))->toBe(\Henzeb\Collection\Lazy\Jsons::class);
});

test('typed strings lazy class method', function () {
    $collection = new \Henzeb\Collection\Typed\Strings(['string1', 'string2']);
    
    $reflection = new ReflectionClass($collection);
    $method = $reflection->getMethod('lazyClass');
    $method->setAccessible(true);
    
    expect($method->invoke($collection))->toBe(\Henzeb\Collection\Lazy\Strings::class);
});