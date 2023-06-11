<?php

namespace Henzeb\Collection;

use Generator;
use Henzeb\Collection\Concerns\HasGenericKeys;
use Henzeb\Collection\Concerns\HasGenerics;
use Illuminate\Support\LazyCollection;

abstract class LazyTypedCollection extends LazyCollection
{
    use HasGenerics, HasGenericKeys;

    public function __construct($source = null)
    {
        $this->validateGenerics();
        $this->validateKeyGenerics();

        parent::__construct(
            function () use ($source): Generator {
                $collection = new LazyCollection($source);
                foreach ($collection as $key => $item) {
                    yield $this->validateKeyTypeAndReturn($key)
                    => $this->validateTypeAndReturn($item);
                }
            }
        );
    }

    /**
     * @param mixed $item
     * @return mixed
     * @throws Exceptions\InvalidTypeException
     */
    private function validateTypeAndReturn(mixed $item): mixed
    {
        $this->validateType($item);

        return $item;
    }

    /**
     * @param mixed $item
     * @return mixed
     * @throws Exceptions\InvalidTypeException
     */
    private function validateKeyTypeAndReturn(mixed $item): mixed
    {
        $this->validateKeyType($item);

        return $item;
    }
}
