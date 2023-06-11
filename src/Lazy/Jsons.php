<?php

namespace Henzeb\Collection\Lazy;

use Henzeb\Collection\Generics\Json;
use Henzeb\Collection\LazyTypedCollection;

class Jsons extends LazyTypedCollection
{
    protected function generics(): string
    {
        return Json::class;
    }
}
