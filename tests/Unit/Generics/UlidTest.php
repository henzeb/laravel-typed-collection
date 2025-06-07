<?php

use Henzeb\Collection\Generics\Ulid;

test('ulid validates', function () {
    expect(Ulid::matchesType('01H3EJ0BZ590C9N423E96YS2NS'))->toBeTrue();
});

test('fails on non string', function () {
    expect(Ulid::matchesType($this))->toBeFalse();
});

test('fails on not ulid string', function () {
    expect(Ulid::matchesType('Not a Uuid'))->toBeFalse();
});

test('fails on invalid ulid', function () {
    expect(Ulid::matchesType('01H3EJ0BZ590C9N423E96YS2N'))->toBeFalse();
});
