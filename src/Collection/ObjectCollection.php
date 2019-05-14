<?php

declare(strict_types=1);

namespace Xparse\ElementFinder\Collection;

use Xparse\ElementFinder\ElementFinderInterface;

/**
 * @author Ivan Shcherbak <alotofall@gmail.com>
 */
class ObjectCollection implements \IteratorAggregate, \Countable
{

    /**
     * @var ElementFinderInterface[]
     */
    private $items;

    /**
     * @var bool
     */
    private $validated = false;


    /**
     * @param ElementFinderInterface[] $items
     * @throws \Exception
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
    final public function last(): ?ElementFinderInterface
    {
        $items = $this->all();
        if ($items === []) {
            return null;
        }
        return end($items);
    }

    /**
     * @throws \InvalidArgumentException
     */
    final public function first(): ?ElementFinderInterface
    {
        $items = $this->all();
        if (\count($items) === 0) {
            return null;
        }
        return reset($items);
    }


    /**
     * @return ElementFinderInterface[]
     * @throws \InvalidArgumentException
     */
    final public function all(): array
    {
        if (!$this->validated) {
            foreach ($this->items as $key => $item) {
                if (!$item instanceof ElementFinderInterface) {
                    $className = ($item === null) ? \gettype($item) : \get_class($item);
                    throw new \InvalidArgumentException(
                        sprintf(
                            'Invalid object type. Expect %s given %s Check item %d',
                            ElementFinderInterface::class,
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
    final public function add(ElementFinderInterface $element): ObjectCollection
    {
        $items = $this->all();
        $items[] = $element;
        return new ObjectCollection($items);
    }


    /**
     * @throws \InvalidArgumentException
     */
    final public function get(int $index): ?ElementFinderInterface
    {
        return $this->all()[$index] ?? null;
    }


    /**
     * @return ElementFinderInterface[]|\Traversable
     * @throws \InvalidArgumentException
     */
    final public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->all());
    }
}
