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
     * @var bool
     */
    private $validated = false;


    /**
     * @param ElementFinder[] $items
     * @throws \Exception
     */
    public function __construct(array $items = [])
    {
        $this->items = $items;
    }


    /**
     * Return number of items in this collection
     *
     * @return int
     * @throws \InvalidArgumentException
     */
    final public function count(): int
    {
        return \count($this->all());
    }


    /**
     * @return null|ElementFinder
     * @throws \InvalidArgumentException
     */
    final public function last()
    {
        $items = $this->all();
        if (\count($items) === 0) {
            return null;
        }
        return end($items);
    }

    /**
     * @return null|ElementFinder
     * @throws \InvalidArgumentException
     */
    final public function first()
    {
        $items = $this->all();
        if (\count($items) === 0) {
            return null;
        }
        return reset($items);
    }


    /**
     * @return ElementFinder[]
     * @throws \InvalidArgumentException
     */
    final public function all(): array
    {
        if (!$this->validated) {
            foreach ($this->items as $key => $item) {
                if (!$item instanceof ElementFinder) {
                    $className = ($item === null) ? \gettype($item) : \get_class($item);
                    throw new \InvalidArgumentException(
                        sprintf(
                            'Invalid object type. Expect %s given %s Check item %d',
                            ElementFinder::class,
                            $className,
                            $key
                        )
                    );
                }
            }
            $this->validated = true;
        }
        return $this->items;
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
     * @throws \InvalidArgumentException
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
     * @throws \InvalidArgumentException
     */
    final public function getIterator()
    {
        return new \ArrayIterator($this->all());
    }
}
