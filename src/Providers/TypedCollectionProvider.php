<?php

namespace Henzeb\Collection\Providers;

use Henzeb\Collection\Mixins\CollectionMixin;
use Illuminate\Support\Collection;
use Illuminate\Support\LazyCollection;
use Illuminate\Support\ServiceProvider;

class TypedCollectionProvider extends ServiceProvider
{
    public function boot(): void
    {
        Collection::mixin(new CollectionMixin());
        LazyCollection::mixin(new CollectionMixin());
    }
}
