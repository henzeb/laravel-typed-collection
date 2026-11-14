<?php

namespace Henzeb\Collection\Concerns;

use Henzeb\Collection\Contracts\DiscardsInvalidTypes;
use Henzeb\Collection\Exceptions\InvalidKeyGenericException;
use Henzeb\Collection\Exceptions\InvalidKeyTypeException;
use Henzeb\Collection\Exceptions\InvalidTypeException;
use Henzeb\Collection\Exceptions\MissingGenericsException;
use Henzeb\Collection\Exceptions\MissingKeyGenericsException;
use Henzeb\Collection\Exceptions\MissingTypedCollectionException;
use Illuminate\Support\LazyCollection;

/**
 * @internal
 */
trait OverridesMethods
{
    /**
     * @throws InvalidKeyGenericException
     * @throws MissingGenericsException
     * @throws MissingKeyGenericsException
     * @throws MissingTypedCollectionException
     */
    public function __construct(
        $items = [],
    ) {
        $this->validateGenerics();
        $this->validateKeyGenerics();

        $items = $this->getArrayableItems($items);

        $this->castTypes($items);
        $this->validateTypes($items);
        $this->validateKeyTypes($items);

        parent::__construct(
            // @phpstan-ignore argument.type
            $items
        );
    }

    protected function lazyClass(): string
    {
        return LazyCollection::class;
    }

    /**
     * @throws InvalidKeyTypeException
     * @throws InvalidTypeException
     */
    public function offsetSet($key, $value): void
    {
        $this->castType($value);

        if ($this instanceof DiscardsInvalidTypes && !$this->accepts($value)) {
            return;
        }

        $this->validateType($value);
        $this->validateKeyType($key);

        // @phpstan-ignore argument.type
        parent::offsetSet($key, $value);
    }

    // @phpstan-ignore selfOut.type
    public function add($item): static
    {
        // @phpstan-ignore argument.type
        return $this->put(null, $item);
    }

    // @phpstan-ignore selfOut.type
    public function push(...$values): static
    {
        $this->castTypes($values);
        $this->validateTypes($values);
        $this->validateKeyTypes($values);

        // @phpstan-ignore argument.type
        return parent::push(...$values);
    }

    /**
     * @throws InvalidTypeException
     */
    // @phpstan-ignore selfOut.type
    public function prepend($value, $key = null): static
    {
        $this->castType($value);

        if ($this instanceof DiscardsInvalidTypes && !$this->accepts($value)) {
            return $this;
        }

        $this->validateType($value);
        $this->validateKeyType($key);

        // @phpstan-ignore argument.type
        return parent::prepend(...func_get_args());
    }

    public function lazy(): LazyCollection
    {
        $lazyClass = $this->lazyClass();
        if ($lazyClass === LazyCollection::class) {
            return parent::lazy();
        }

        // @phpstan-ignore return.type
        return new $lazyClass($this->all());
    }

    public function chunk($size, $preserveKeys = true)
    {
        // @phpstan-ignore return.type
        return $this->collect()
            ->chunk($size, $preserveKeys)
            ->mapInto(static::class);
    }

    public function map(callable $callback)
    {
        // @phpstan-ignore return.type
        return $this->collect()->map($callback);
    }

    public function mapWithKeys(callable $callback)
    {
        // @phpstan-ignore return.type
        return $this->collect()->mapWithKeys($callback);
    }

    public function mapToDictionary(callable $callback)
    {
        // @phpstan-ignore return.type
        return $this->collect()->mapToDictionary($callback);
    }

    public function keys()
    {
        // @phpstan-ignore return.type
        return $this->collect()->keys();
    }
}
