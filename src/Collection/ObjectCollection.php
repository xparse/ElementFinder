<?php

declare(strict_types=1);

namespace Xparse\ElementFinder\Collection;

use Xparse\ElementFinder\ElementFinder;

/**
 * @author Ivan Shcherbak <alotofall@gmail.com>
 */
class ObjectCollection implements \IteratorAggregate, \Countable
{

    /**
     * @var ElementFinder[]
     */
    private $items;


    /**
     * @param ElementFinder[] $items
     * @throws \Exception
     */
    public function __construct(array $items = [])
    {
        foreach ($items as $key => $item) {
            if (!$item instanceof ElementFinder) {
                $className = ($item === null) ? gettype($item) : get_class($item);
                throw new \InvalidArgumentException('Invalid object type. Expect ' . ElementFinder::class . ' given ' . $className);
            }
        }
        $this->items = $items;
    }


    /**
     * Return number of items in this collection
     *
     * @return int
     */
    final public function count(): int
    {
        return count($this->all());
    }


    /**
     * Return last item from collection
     *
     * @return null|ElementFinder
     */
    final public function getLast()
    {
        $items = $this->all();
        if (count($items) === 0) {
            return null;
        }
        return end($items);
    }


    /**
     * Return first item from collection
     *
     * @return null|ElementFinder
     */
    final public function getFirst()
    {
        $items = $this->all();
        if (count($items) === 0) {
            return null;
        }
        return reset($items);
    }


    /**
     * @return ElementFinder[]
     */
    final public function all(): array
    {
        return $this->items;
    }

    /**
     * @deprecated
     * @see all
     * @return ElementFinder[]
     */
    final public function getItems(): array
    {
        trigger_error('Deprecated. See all', E_USER_DEPRECATED);
        return $this->items;
    }


    /**
     * @deprecated
     * @param callable $callback
     * @return self
     */
    final public function walk(callable $callback): self
    {
        trigger_error('Deprecated', E_USER_DEPRECATED);
        foreach ($this->all() as $index => $item) {
            $callback($item, $index, $this);
        }
        return $this;
    }


    /**
     * @param ObjectCollection $collection
     * @return ObjectCollection
     * @throws \Exception
     */
    final public function merge(ObjectCollection $collection): ObjectCollection
    {
        return new ObjectCollection(array_merge($this->all(), $collection->all()));
    }


    /**
     * @param ElementFinder $element
     * @return ObjectCollection
     * @throws \Exception
     */
    final public function add(ElementFinder $element): ObjectCollection
    {
        $items = $this->all();
        $items[] = $element;
        return new ObjectCollection($items);
    }


    /**
     * @param int $index
     * @return null|ElementFinder
     */
    final public function get(int $index)
    {
        $items = $this->all();
        if (array_key_exists($index, $items)) {
            return $items[$index];
        }
        return null;
    }


    /**
     * @link http://php.net/manual/en/iteratoraggregate.getiterator.php
     * @return ElementFinder[]|\ArrayIterator
     */
    final public function getIterator()
    {
        return new \ArrayIterator($this->all());
    }
}
