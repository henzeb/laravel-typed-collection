<?php

namespace Henzeb\Collection\Typed;

use Henzeb\Collection\Generics\Json;
use Henzeb\Collection\Lazy\Jsons as LazyJsons;
use Henzeb\Collection\TypedCollection;

/**
 * @extends TypedCollection<integer|string, string>
 */
class Jsons extends TypedCollection
{
    protected function generics(): string
    {
        return Json::class;
    }

    protected function lazyClass(): string
    {
        return LazyJsons::class;
    }
}
