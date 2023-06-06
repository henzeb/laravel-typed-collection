<?php

namespace Henzeb\Collection\Concerns;

use Henzeb\Collection\Enums\Type;

trait CollectsGenerics
{
    private function collectGenerics(): Type|string|array|null
    {
        /**
         * @infection-ignore-all
         */
        $backtrace = debug_backtrace(
            DEBUG_BACKTRACE_IGNORE_ARGS | DEBUG_BACKTRACE_PROVIDE_OBJECT,
            3
        );

        return end($backtrace)['object']->generics();
    }
}
