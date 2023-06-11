<?php

namespace Henzeb\Collection\Concerns;

use Henzeb\Collection\Enums\Type;
use Henzeb\Collection\LazyTypedCollection;
use Henzeb\Collection\TypedCollection;

trait CollectsGenerics
{
    private function getParentObject(): TypedCollection|LazyTypedCollection|null
    {
        /**
         * @infection-ignore-all
         */
        $backtrace = debug_backtrace(
            DEBUG_BACKTRACE_IGNORE_ARGS | DEBUG_BACKTRACE_PROVIDE_OBJECT,
            4
        );

        $object = end($backtrace)['object'];

        if ($object instanceof TypedCollection || $object instanceof LazyTypedCollection) {
            return $object;
        }

        return null;
    }

    private function collectGenerics(): Type|string|array|null
    {
        return $this->getParentObject()?->generics();
    }

    private function collectKeyGenerics(): Type|string|array|null
    {
        return $this->getParentObject()?->keyGenerics();
    }
}
