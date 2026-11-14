<?php

namespace Henzeb\Collection\Concerns;

/**
 * @internal
 */
trait CollectsGenerics
{
    private function getParentObject(): ?object
    {
        /**
         * @infection-ignore-all
         */
        $backtrace = debug_backtrace(
            DEBUG_BACKTRACE_IGNORE_ARGS | DEBUG_BACKTRACE_PROVIDE_OBJECT,
            4
        );

        return end($backtrace)['object'] ?? null;
    }
}
