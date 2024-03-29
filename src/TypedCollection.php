<?php

namespace Henzeb\Collection;

use Henzeb\Collection\Concerns\HasGenericKeys;
use Henzeb\Collection\Concerns\HasGenerics;
use Henzeb\Collection\Concerns\OverridesMethods;
use Illuminate\Support\Collection;

/**
 * @template TKey of array-key
 *
 * @template-covariant TValue
 *
 * @extends Collection<TKey, TValue>
 */
abstract class TypedCollection extends Collection
{
    use HasGenerics, HasGenericKeys, OverridesMethods;
}
