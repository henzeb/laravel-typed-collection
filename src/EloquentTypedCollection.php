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
 * @template TModel of \Illuminate\Database\Eloquent\Model
 *
 * @extends Collection<TKey, TModel>
 */
abstract class EloquentTypedCollection extends Collection
{
    use HasGenerics;
    use HasGenericKeys;
    use OverridesMethods;
    use HasBaseClass;
}
