<?php

declare(strict_types=1);

namespace Xparse\ElementFinder\Collection;

use Xparse\ElementFinder\ElementFinder\Element;

/**
 * @author Ivan Shcherbak <alotofall@gmail.com>
 */
class ElementCollection implements \IteratorAggregate, \Countable
{

    /**
     * @var Element[]
     */
    private $items;


    /**
     * @param Element[] $items
     * @throws \InvalidArgumentException
     */
    public function __construct(array $items = [])
    {
        foreach ($items as $item) {
            if (!$item instanceof Element) {
                $className = ($item === null) ? gettype($item) : get_class($item);
                throw new \InvalidArgumentException('Invalid object type. Expect ' . Element::class . ' given ' . $className);
            }
        }
        $this->items = $items;
    }


    final public function count(): int
    {
        return count($this->getItems());
    }


    /**
     * @return Element|null
     */
    final public function getLast()
    {
        $items = $this->getItems();
        if (count($items) === 0) {
            return null;
        }
        return end($items);
    }


    /**
     * @return Element|null
     */
    final public function getFirst()
    {
        $items = $this->getItems();
        if (count($items) === 0) {
            return null;
        }
        return reset($items);
    }


    /**
     * @param int $index
     * @return Element|null
     */
    final public function get(int $index)
    {
        $items = $this->getItems();
        if (array_key_exists($index, $items)) {
            return $items[$index];
        }
        return null;
    }


    /**
     * Return array of items connected to this collection
     *
     * Rewrite this method in you class
     *
     * <code>
     * foreach($collection->getItems() as $item){
     *  echo get_class($item)."\n;
     * }
     * </code>
     *
     * @return Element[]
     */
    final public function getItems(): array
    {
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
        foreach ($this->getItems() as $index => $item) {
            $callback($item, $index, $this);
        }
        return $this;
    }


    /**
     * @param ElementCollection $collection
     * @return ElementCollection
     * @throws \InvalidArgumentException
     */
    final public function merge(ElementCollection $collection): ElementCollection
    {
        return new ElementCollection(array_merge($this->getItems(), $collection->getItems()));
    }


    /**
     * @param Element $element
     * @return ElementCollection
     * @throws \InvalidArgumentException
     */
    final public function add(Element $element): ElementCollection
    {
        $items = $this->getItems();
        $items[] = $element;
        return new ElementCollection($items);
    }


    /**
     * Retrieve an external iterator
     *
     * @link http://php.net/manual/en/iteratoraggregate.getiterator.php
     * @return Element[]|\ArrayIterator An instance of an object implementing Iterator or Traversable
     */
    final public function getIterator()
    {
        return new \ArrayIterator($this->getItems());
    }
}
