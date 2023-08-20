<?php
namespace Jonathan13779\Database\Model;

use IteratorAggregate;
use ArrayIterator;

class Collection implements IteratorAggregate 
{

    protected array $items = [];

    public function __construct(array $items = [])
    {
        $this->items = $items;
    }

    public function add($item): void
    {
        $this->items[] = $item;
    }

    public function getIterator(): iterable
    {
        return new ArrayIterator($this->items);
    }

    public function toArray(): array
    {
        return $this->items;
    }

    public function first(): mixed
    {
        return $this->items[0];
    }

    public function last(): mixed
    {
        return $this->items[count($this->items) - 1];
    }

    public function count(): int
    {
        return count($this->items);
    }

    public function map(callable $callback): Collection
    {
        return new static(array_map($callback, $this->items));
    }

    public function filter(callable $callback): Collection
    {
        return new static(array_filter($this->items, $callback));
    }

    public function reduce(callable $callback, $initial = null): mixed
    {
        return array_reduce($this->items, $callback, $initial);
    }

    public function each(callable $callback): void
    {
        foreach ($this->items as $item) {
            $callback($item);
        }
    }

    public function keyBy(string $key): Collection
    {
        return new static(array_reduce($this->items, function ($carry, $item) use ($key) {
            $carry[$item[$key]] = $item;
            return $carry;
        }, []));
    }

    public function pluck(string $key): Collection
    {
        return new static(array_map(function ($item) use ($key) {
            return $item[$key];
        }, $this->items));
    }

    public function unique(): Collection
    {
        return new static(array_unique($this->items));
    }

    public function uniqueBy(string $key): Collection
    {
        return new static(array_reduce($this->items, function ($carry, $item) use ($key) {
            if (!in_array($item[$key], $carry)) {
                $carry[] = $item[$key];
            }
            return $carry;
        }, []));
    }

    public function diff(Collection $collection): Collection
    {
        return new static(array_diff($this->items, $collection->toArray()));
    }

    public function diffBy(string $key, Collection $collection): Collection
    {
        return new static(array_filter($this->items, function ($item) use ($key, $collection) {
            return !$collection->contains($key, $item[$key]);
        }));
    }

    public function contains(string $key, $value): bool
    {
        return $this->firstWhere($key, $value) !== null;
    }

    public function firstWhere(string $key, $value): mixed
    {
        foreach ($this->items as $item) {
            if ($item[$key] === $value) {
                return $item;
            }
        }
        return null;
    }

    public function sortBy(string $key): Collection
    {
        $items = $this->items;
        usort($items, function ($a, $b) use ($key) {
            return $a[$key] <=> $b[$key];
        });
        return new static($items);
    }

    public function sortByDesc(string $key): Collection
    {
        $items = $this->items;
        usort($items, function ($a, $b) use ($key) {
            return $b[$key] <=> $a[$key];
        });
        return new static($items);
    }

    public function groupBy(string $key): Collection
    {
        return new static(array_reduce($this->items, function ($carry, $item) use ($key) {
            $carry[$item[$key]][] = $item;
            return $carry;
        }, []));
    }

    public function chunk(int $size): Collection
    {
        return new static(array_chunk($this->items, $size));
    }

    public function chunkBy(int $size, string $key): Collection
    {
        $chunks = [];
        $chunk = [];
        foreach ($this->items as $item) {
            if (count($chunk) === 0) {
                $chunk[] = $item;
            } else if ($chunk[count($chunk) - 1][$key] === $item[$key]) {
                $chunk[] = $item;
            } else {
                $chunks[] = $chunk;
                $chunk = [$item];
            }
        }
        if (count($chunk) > 0) {
            $chunks[] = $chunk;
        }
        return new static($chunks);
    }

    public function chunkByCallback(int $size, callable $callback): Collection
    {
        $chunks = [];
        $chunk = [];
        foreach ($this->items as $item) {
            if (count($chunk) === 0) {
                $chunk[] = $item;
            } else if ($callback($chunk[count($chunk) - 1], $item)) {
                $chunk[] = $item;
            } else {
                $chunks[] = $chunk;
                $chunk = [$item];
            }
        }
        if (count($chunk) > 0) {
            $chunks[] = $chunk;
        }
        return new static($chunks);
    }

    public function chunkByKeys(int $size, array $keys): Collection
    {
        $chunks = [];
        $chunk = [];
        foreach ($this->items as $item) {
            if (count($chunk) === 0) {
                $chunk[] = $item;
            } else if ($this->compareKeys($chunk[count($chunk) - 1], $item, $keys)) {
                $chunk[] = $item;
            } else {
                $chunks[] = $chunk;
                $chunk = [$item];
            }
        }
        if (count($chunk) > 0) {
            $chunks[] = $chunk;
        }
        return new static($chunks);
    }

    private function compareKeys($a, $b, $keys): bool
    {
        foreach ($keys as $key) {
            if ($a[$key] !== $b[$key]) {
                return false;
            }
        }
        return true;
    }

    public function chunkByKeysCallback(int $size, array $keys, callable $callback): Collection
    {
        $chunks = [];
        $chunk = [];
        foreach ($this->items as $item) {
            if (count($chunk) === 0) {
                $chunk[] = $item;
            } else if ($callback($chunk[count($chunk) - 1], $item, $keys)) {
                $chunk[] = $item;
            } else {
                $chunks[] = $chunk;
                $chunk = [$item];
            }
        }
        if (count($chunk) > 0) {
            $chunks[] = $chunk;
        }
        return new static($chunks);
    }

    public function chunkByKeysAndCallback(int $size, array $keys, callable $callback): Collection
    {
        $chunks = [];
        $chunk = [];
        foreach ($this->items as $item) {
            if (count($chunk) === 0) {
                $chunk[] = $item;
            } else if ($this->compareKeys($chunk[count($chunk) - 1], $item, $keys) && $callback($chunk[count($chunk) - 1], $item)) {
                $chunk[] = $item;
            } else {
                $chunks[] = $chunk;
                $chunk = [$item];
            }
        }
        if (count($chunk) > 0) {
            $chunks[] = $chunk;
        }
        return new static($chunks);
    }

    public function chunkByKeysAndCallbackAndSize(int $size, array $keys, callable $callback): Collection
    {
        $chunks = [];
        $chunk = [];
        foreach ($this->items as $item) {
            if (count($chunk) === 0) {
                $chunk[] = $item;
            } else if ($this->compareKeys($chunk[count($chunk) - 1], $item, $keys) && $callback($chunk[count($chunk) - 1], $item) && count($chunk) < $size) {
                $chunk[] = $item;
            } else {
                $chunks[] = $chunk;
                $chunk = [$item];
            }
        }
        if (count($chunk) > 0) {
            $chunks[] = $chunk;
        }
        return new static($chunks);
    }

    public function chunkByKeysAndCallbackAndSizeAndCallback(int $size, array $keys, callable $callback, callable $callback2): Collection
    {
        $chunks = [];
        $chunk = [];
        foreach ($this->items as $item) {
            if (count($chunk) === 0) {
                $chunk[] = $item;
            } else if ($this->compareKeys($chunk[count($chunk) - 1], $item, $keys) && $callback($chunk[count($chunk) - 1], $item) && count($chunk) < $size && $callback2($chunk[count($chunk) - 1], $item)) {
                $chunk[] = $item;
            } else {
                $chunks[] = $chunk;
                $chunk = [$item];
            }
        }
        if (count($chunk) > 0) {
            $chunks[] = $chunk;
        }
        return new static($chunks);
    }

    public function chunkByKeysAndCallbackAndSizeAndCallbackAndSize(int $size, array $keys, callable $callback, callable $callback2, int $size2): Collection
    {
        $chunks = [];
        $chunk = [];
        foreach ($this->items as $item) {
            if (count($chunk) === 0) {
                $chunk[] = $item;
            } else if ($this->compareKeys($chunk[count($chunk) - 1], $item, $keys) && $callback($chunk[count($chunk) - 1], $item) && count($chunk) < $size && $callback2($chunk[count($chunk) - 1], $item) && count($chunk) < $size2) {
                $chunk[] = $item;
            } else {
                $chunks[] = $chunk;
                $chunk = [$item];
            }
        }
        if (count($chunk) > 0) {
            $chunks[] = $chunk;
        }
        return new static($chunks);
    }
    
    public function chunkByKeysAndCallbackAndSizeAndCallbackAndSizeAndCallback(int $size, array $keys, callable $callback, callable $callback2, int $size2, callable $callback3): Collection
    {
        $chunks = [];
        $chunk = [];
        foreach ($this->items as $item) {
            if (count($chunk) === 0) {
                $chunk[] = $item;
            } else if ($this->compareKeys($chunk[count($chunk) - 1], $item, $keys) && $callback($chunk[count($chunk) - 1], $item) && count($chunk) < $size && $callback2($chunk[count($chunk) - 1], $item) && count($chunk) < $size2 && $callback3($chunk[count($chunk) - 1], $item)) {
                $chunk[] = $item;
            } else {
                $chunks[] = $chunk;
                $chunk = [$item];
            }
        }
        if (count($chunk) > 0) {
            $chunks[] = $chunk;
        }
        return new static($chunks);
    }

    public function chunkByKeysAndCallbackAndSizeAndCallbackAndSizeAndCallbackAndSize(int $size, array $keys, callable $callback, callable $callback2, int $size2, callable $callback3, int $size3): Collection
    {
        $chunks = [];
        $chunk = [];
        foreach ($this->items as $item) {
            if (count($chunk) === 0) {
                $chunk[] = $item;
            } else if ($this->compareKeys($chunk[count($chunk) - 1], $item, $keys) && $callback($chunk[count($chunk) - 1], $item) && count($chunk) < $size && $callback2($chunk[count($chunk) - 1], $item) && count($chunk) < $size2 && $callback3($chunk[count($chunk) - 1], $item) && count($chunk) < $size3) {
                $chunk[] = $item;
            } else {
                $chunks[] = $chunk;
                $chunk = [$item];
            }
        }
        if (count($chunk) > 0) {
            $chunks[] = $chunk;
        }
        return new static($chunks);
    }

    public function chunkByKeysAndCallbackAndSizeAndCallbackAndSizeAndCallbackAndSizeAndCallback(int $size, array $keys, callable $callback, callable $callback2, int $size2, callable $callback3, int $size3, callable $callback4): Collection
    {
        $chunks = [];
        $chunk = [];
        foreach ($this->items as $item) {
            if (count($chunk) === 0) {
                $chunk[] = $item;
            } else if ($this->compareKeys($chunk[count($chunk) - 1], $item, $keys) && $callback($chunk[count($chunk) - 1], $item) && count($chunk) < $size && $callback2($chunk[count($chunk) - 1], $item) && count($chunk) < $size2 && $callback3($chunk[count($chunk) - 1], $item) && count($chunk) < $size3 && $callback4($chunk[count($chunk) - 1], $item)) {
                $chunk[] = $item;
            } else {
                $chunks[] = $chunk;
                $chunk = [$item];
            }
        }
        if (count($chunk) > 0) {
            $chunks[] = $chunk;
        }
        return new static($chunks);
    }
}    