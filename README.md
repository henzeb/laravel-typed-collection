# Laravel Typed Collection

[![Build Status](https://github.com/henzeb/laravel-typed-collection/workflows/tests/badge.svg)](https://github.com/henzeb/laravel-typed-collection/actions)
[![Latest Version on Packagist](https://img.shields.io/packagist/v/henzeb/laravel-typed-collection.svg?style=flat-square)](https://packagist.org/packages/henzeb/laravel-typed-collection)
[![Total Downloads](https://img.shields.io/packagist/dt/henzeb/laravel-typed-collection.svg?style=flat-square)](https://packagist.org/packages/henzeb/laravel-typed-collection)
[![License](https://img.shields.io/packagist/l/henzeb/laravel-typed-collection)](https://packagist.org/packages/henzeb/laravel-typed-collection)

PHP has no support for Generics. Yet sometimes, we want to be sure
we receive an array of a certain type.

Using Laravel's Collections, we should be able to force types. This
package allows you to.

Unlike many other packages, this one has support for
[Eloquent](#typed-collections-in-eloquent)!

## Installation

Just install with the following command.

```bash
composer require henzeb/laravel-typed-collection
```

## usage

- [Generic Typed Collections](docs/generic.md)
- [Returnable Typed Collections](docs/returnable.md)
    - [Returnable Lazy Typed Collections](docs/returnable.md#lazy-collections)
    - [Discarding Invalid Types](docs/returnable.md#discarding-invalid-types)
- [Types and Collections](docs/types.md)
    - [Custom Generic Types](docs/types.md#custom-generic-types)
- [Helper Methods](docs/helpers.md)
- [Casting](docs/casting.md)
- [Eloquent](docs/eloquent.md)

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
