<?php

namespace Henzeb\Collection;

use Henzeb\Collection\Concerns\HasGenericKeys;
use Henzeb\Collection\Concerns\HasGenerics;
use Illuminate\Support\Collection;
use Illuminate\Support\LazyCollection;

/**
 * @template TKey of array-key
 *
 * @template-covariant TValue
 *
 * @extends Collection<TKey, TValue>
 */
abstract class TypedCollection extends Collection
{
    use HasGenerics, HasGenericKeys;

    public function __construct(
        $items = [],
    ) {
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
}
