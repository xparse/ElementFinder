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
     * @throws \InvalidArgumentException
     */
    final public function count(): int
    {
        return \count($this->all());
    }


    /**
     * @throws \InvalidArgumentException
     */
    final public function last(): ?Element
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
    final public function first(): ?Element
    {
        $items = $this->all();
        if (\count($items) === 0) {
            return null;
        }
        return reset($items);
    }


    /**
     * @throws \InvalidArgumentException
     */
    final public function get(int $index): ?Element
    {
        return $this->all()[$index] ?? null;
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
     * @throws \InvalidArgumentException
     */
    final public function merge(ElementCollection $collection): ElementCollection
    {
        return new ElementCollection(array_merge($this->all(), $collection->all()));
    }


    /**
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
     * @return Element[]|\Traversable An instance of an object implementing Iterator or Traversable
     * @throws \InvalidArgumentException
     */
    final public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->all());
    }
}
