<?php

namespace Henzeb\Collection;

use Generator;
use Henzeb\Collection\Concerns\HasGenerics;
use Illuminate\Support\LazyCollection;

abstract class LazyTypedCollection extends LazyCollection
{
    use HasGenerics;

    public function __construct($source = null)
    {
        $this->validateGenerics();

        parent::__construct(
            function () use ($source): Generator {
                $collection = new LazyCollection($source);
                foreach ($collection as $key => $item) {
                    yield $key => $this->validateTypeAndReturn($item);
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
}
