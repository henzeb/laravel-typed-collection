<?php

namespace Henzeb\Collection;

use Henzeb\Collection\Concerns\HasBaseClass;
use Henzeb\Collection\Concerns\HasGenericKeys;
use Henzeb\Collection\Concerns\HasGenerics;
use Henzeb\Collection\Concerns\OverridesMethods;
use Illuminate\Database\Eloquent\Collection;

/**
 * @template TKey of array-key
 *
 * @template-covariant TValue
 *
 * @extends Collection<TKey, TValue>
 */
abstract class EloquentTypedCollection extends Collection
{
    use HasGenerics, HasGenericKeys, OverridesMethods, HasBaseClass;
}
