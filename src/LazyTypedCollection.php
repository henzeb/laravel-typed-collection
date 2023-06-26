<?php

namespace Henzeb\Collection;

use Generator;
use Henzeb\Collection\Concerns\HasGenericKeys;
use Henzeb\Collection\Concerns\HasGenerics;
use Illuminate\Support\LazyCollection;

/**
 * @template TKey of array-key
 *
 * @template-covariant TValue
 *
 * @extends LazyCollection<TKey, TValue>
 */
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

    public function chunkWhile(callable $callback)
    {
        return (new LazyCollection(
            $this
        ))->chunkWhile($callback)
            ->mapInto(static::class);
    }

    public function chunk($size)
    {
        return (new LazyCollection(
            $this
        ))->chunk($size)
            ->mapInto(static::class);
    }

    public function map(callable $callback)
    {
        return (new LazyCollection(
            $this
        ))->map($callback);
    }

    public function mapWithKeys(callable $callback): LazyCollection
    {
        return (new LazyCollection(
            $this
        ))->mapWithKeys($callback);
    }

    public function mapToDictionary(callable $callback): LazyCollection
    {
        return (new LazyCollection(
            $this
        ))->mapToDictionary($callback);
    }
}
