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
     * @var bool
     */
    private $validated = false;


    /**
     * @param Element[] $items
     * @throws \InvalidArgumentException
     */
    public function __construct(array $items = [])
    {
        $this->items = $items;
    }


    /**
     * @return int
     * @throws \InvalidArgumentException
     */
    final public function count(): int
    {
        return \count($this->all());
    }


    /**
     * @return Element|null
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
     * @return Element|null
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
     * @param int $index
     * @return Element|null
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
     * @return Element[]
     * @throws \InvalidArgumentException
     */
    final public function all(): array
    {
        if (!$this->validated) {
            foreach ($this->items as $key => $item) {
                if (!$item instanceof Element) {
                    $className = ($item === null) ? \gettype($item) : \get_class($item);
                    throw new \InvalidArgumentException(
                        sprintf(
                            'Invalid object type. Expect %s given %s Check item %d',
                            Element::class,
                            $className,
                            $key
                        )
                    );
                }
            }
        }

        return $this->items;
    }

    /**
     * @param ElementCollection $collection
     * @return ElementCollection
     * @throws \InvalidArgumentException
     */
    final public function merge(ElementCollection $collection): ElementCollection
    {
        return new ElementCollection(array_merge($this->all(), $collection->all()));
    }


    /**
     * @param Element $element
     * @return ElementCollection
     * @throws \InvalidArgumentException
     */
    final public function add(Element $element): ElementCollection
    {
        $items = $this->all();
        $items[] = $element;
        return new ElementCollection($items);
    }


    /**
     * Retrieve an external iterator
     *
     * @link http://php.net/manual/en/iteratoraggregate.getiterator.php
     * @return Element[]|\ArrayIterator An instance of an object implementing Iterator or Traversable
     * @throws \InvalidArgumentException
     */
    final public function getIterator()
    {
        return new \ArrayIterator($this->all());
    }
}
