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
     * @throws \InvalidArgumentException
     */
    final public function count(): int
    {
        return \count($this->all());
    }


    /**
     * @throws \InvalidArgumentException
     */
    final public function last(): ?ElementFinder
    {
        $items = $this->all();
        if (\count($items) === 0) {
            return null;
        }
        return end($items);
    }

    /**
     * @throws \InvalidArgumentException
     */
    final public function first(): ?ElementFinder
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
     * @throws \Exception
     */
    final public function merge(ObjectCollection $collection): ObjectCollection
    {
        return new ObjectCollection(array_merge($this->all(), $collection->all()));
    }


    /**
     * @throws \Exception
     */
    final public function add(ElementFinder $element): ObjectCollection
    {
        $items = $this->all();
        $items[] = $element;
        return new ObjectCollection($items);
    }


    /**
     * @throws \InvalidArgumentException
     */
    final public function get(int $index): ?ElementFinder
    {
        return $this->all()[$index] ?? null;
    }


    /**
     * @link http://php.net/manual/en/iteratoraggregate.getiterator.php
     * @return ElementFinder[]|\Traversable
     * @throws \InvalidArgumentException
     */
    final public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->all());
    }
}
