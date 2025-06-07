<?php

use Henzeb\Collection\Generics\Uuid;

test('uuid validates', function () {
    expect(Uuid::matchesType('1b2a5269-de6f-4e19-a75a-06c0262dbcd7'))->toBeTrue();
});

test('fails on non string', function () {
    expect(Uuid::matchesType($this))->toBeFalse();
});

test('fails on not uuid string', function () {
    expect(Uuid::matchesType('Not a Uuid'))->toBeFalse();
});

test('fails on invalid uuid', function () {
    expect(Uuid::matchesType('1b2a5269-de6f-4e19-a75a-06c0262dbcd'))->toBeFalse();
});
