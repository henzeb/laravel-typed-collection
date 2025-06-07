<?php

namespace Henzeb\Collection;

use Generator;
use Henzeb\Collection\Concerns\HasBaseClass;
use Henzeb\Collection\Concerns\HasGenericKeys;
use Henzeb\Collection\Concerns\HasGenerics;
use Henzeb\Collection\Contracts\DiscardsInvalidTypes;
use Henzeb\Collection\Exceptions\InvalidKeyGenericException;
use Henzeb\Collection\Exceptions\InvalidKeyTypeException;
use Henzeb\Collection\Exceptions\InvalidTypeException;
use Henzeb\Collection\Exceptions\MissingGenericsException;
use Henzeb\Collection\Exceptions\MissingKeyGenericsException;
use Henzeb\Collection\Exceptions\MissingTypedCollectionException;
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
    use HasGenerics, HasGenericKeys, HasBaseClass;

    /**
     * @throws InvalidKeyGenericException
     * @throws MissingGenericsException
     * @throws MissingKeyGenericsException
     * @throws MissingTypedCollectionException
     */
    public function __construct($source = null)
    {
        $this->validateGenerics();
        $this->validateKeyGenerics();

        parent::__construct(
            function () use ($source): Generator {
                $collection = new LazyCollection($source);
                foreach ($collection as $key => $item) {
                    if ($this instanceof DiscardsInvalidTypes && !$this->accepts($item)) {
                        continue;
                    }
                    yield $this->validateKeyTypeAndReturn($key)
                    => $this->castAndValidateTypeAndReturn($item);
                }
            }
        );
    }

    /**
     * @param mixed $item
     * @return mixed
     * @throws InvalidTypeException
     */
    private function castAndValidateTypeAndReturn(mixed $item): mixed
    {
        $this->castType($item);
        $this->validateType($item);

        return $item;
    }

    /**
     * @param mixed $item
     * @return mixed
     * @throws InvalidKeyTypeException
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

    public function chunk($size, $preserveKeys = true)
    {
        return (new LazyCollection(
            $this
        ))->chunk($size, $preserveKeys)
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

    public function keys()
    {
        return (new LazyCollection($this))->keys();
    }
}
