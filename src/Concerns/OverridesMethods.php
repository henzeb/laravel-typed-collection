<?php

namespace Henzeb\Collection\Concerns;

use Illuminate\Support\LazyCollection;

/**
 * @internal
 */
trait OverridesMethods
{
    public function __construct(
        $items = [],
    )
    {
        $this->validateGenerics();
        $this->validateKeyGenerics();

        $items = $this->getArrayableItems($items);

        $this->validateTypes($items);
        $this->validateKeyTypes($items);

        parent::__construct(
            $items
        );
    }

    protected function lazyClass(): string
    {
        return LazyCollection::class;
    }

    public function offsetSet($key, $value): void
    {
        $this->validateType($value);
        $this->validateKeyType($key);

        parent::offsetSet($key, $value);
    }

    public function add($item): static
    {
        return $this->put(null, $item);
    }

    public function push(...$values): static
    {
        $this->validateTypes($values);
        $this->validateKeyTypes($values);

        return parent::push(...$values);
    }

    public function prepend($value, $key = null): static
    {
        $this->validateType($value);

        return parent::prepend(...func_get_args());
    }

    public function lazy(): LazyCollection
    {
        $lazyClass = $this->lazyClass();
        if ($lazyClass === LazyCollection::class) {
            return parent::lazy();
        }

        return new $lazyClass($this->all());
    }

    public function chunk($size)
    {
        return $this->collect()
            ->chunk($size)
            ->mapInto(static::class);
    }

    public function map(callable $callback)
    {
        return $this->collect()->map($callback);
    }

    public function mapWithKeys(callable $callback)
    {
        return $this->collect()->mapWithKeys($callback);
    }

    public function mapToDictionary(callable $callback)
    {
        return $this->collect()->mapToDictionary($callback);
    }
}
