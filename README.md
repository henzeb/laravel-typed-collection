# Laravel Typed Collection

[![Build Status](https://github.com/henzeb/laravel-typed-collection/workflows/tests/badge.svg)](https://github.com/henzeb/laravel-typed-collection/actions)
[![Latest Version on Packagist](https://img.shields.io/packagist/v/henzeb/laravel-typed-collection.svg?style=flat-square)](https://packagist.org/packages/henzeb/laravel-typed-collection)
[![Total Downloads](https://img.shields.io/packagist/dt/henzeb/laravel-typed-collection.svg?style=flat-square)](https://packagist.org/packages/henzeb/laravel-typed-collection)
[![Test Coverage](https://api.codeclimate.com/v1/badges/b33a1948230c629a3c54/test_coverage)](https://codeclimate.com/github/henzeb/laravel-typed-collection/test_coverage)
[![License](https://img.shields.io/packagist/l/henzeb/laravel-typed-collection)](https://packagist.org/packages/henzeb/laravel-typed-collection)

PHP has no support for Generics. Yet sometimes, we want to be sure
we receive an array of a certain type.

Using Laravel's Collections, we should be able to force types. This
package allows you to.

## Installation

Just install with the following command.

```bash
composer require henzeb/laravel-typed-collection
```

## usage

````php
use Henzeb\Collection\Enums\Type;

$collection = collect()->withGenerics(Type::String, Post::class);

$collection->add('Hello World'); // succeeds
$collection->put('post',new Post()); // succeeds
$collection[] = new User(); // fails
````

### Extending TypedCollection

````php
use Henzeb\Collection\TypedCollection;
use Henzeb\Collection\Enums\Type;

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
````

## Testing this package

```bash
composer test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email
henzeberkheij@gmail.com instead of using the issue tracker.

## Credits

- [Henze Berkheij](https://github.com/henzeb)

## License

The GNU AGPLv. Please see [License File](LICENSE.md) for more information.
