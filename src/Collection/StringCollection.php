<?php

declare(strict_types=1);

namespace Xparse\ElementFinder\Collection;

use Xparse\ElementFinder\Collection\Filters\StringFilter\StringFilterInterface;
use Xparse\ElementFinder\Collection\Modify\StringModify\StringModifyInterface;
use Xparse\ElementFinder\Helper\RegexHelper;

/**
 * @author Ivan Shcherbak <alotofall@gmail.com>
 */
class StringCollection implements \IteratorAggregate, \Countable
{


    /**
     * @var string[]
     */
    private $items = [];


    /**
     * @param string[] $items
     * @throws \Exception
     */
    public function __construct(array $items = [])
    {
        foreach ($items as $key => $item) {
            if (!is_string($item)) {
                throw new \InvalidArgumentException('Expect string');
            }
        }
        $this->items = array_values($items);
    }


    final public function count(): int
    {
        return count($this->getItems());
    }


    /**
     * @return null|string
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
     * @return null|string
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
     * @return string[]
     */
    final public function getItems(): array
    {
        return $this->items;
    }


    /**
     * @param callable $callback
     * @return StringCollection
     */
    final public function walk(callable $callback): StringCollection
    {
        foreach ($this->getItems() as $index => $item) {
            $callback($item, $index, $this);
        }
        return $this;
    }


    final public function map(StringModifyInterface $modifier): StringCollection
    {
        $items = [];
        foreach ($this->getItems() as $item) {
            $items[] = $modifier->modify($item);
        }
        return new StringCollection($items);
    }


    final public function filter(StringFilterInterface $filter): StringCollection
    {
        $items = [];
        foreach ($this->getItems() as $item) {
            if ($filter->valid($item)) {
                $items[] = $item;
            }
        }
        return new StringCollection($items);
    }


    final public function replace(string $regexp, string $to = null): StringCollection
    {
        if (null === $to) {
            trigger_error('Require second parameter $to', E_USER_DEPRECATED);
        }
        $result = [];
        foreach ($this->getItems() as $index => $item) {
            $result[] = preg_replace($regexp, $to, $item);
        }
        return new StringCollection($result);
    }


    final public function match(string $regexp, int $index = 1): StringCollection
    {
        return RegexHelper::match($regexp, $index, $this->getItems());
    }


    final public function split(string $regexp): StringCollection
    {
        $items = [];
        foreach ($this->getItems() as $item) {
            $data = preg_split($regexp, $item);
            foreach ($data as $string) {
                $items[] = $string;
            }
        }
        return new StringCollection($items);
    }


    final public function unique(): StringCollection
    {
        return new StringCollection(array_unique($this->getItems()));
    }


    final public function merge(StringCollection $collection): StringCollection
    {
        return new StringCollection(array_merge($this->getItems(), $collection->getItems()));
    }


    final public function add(string $item): StringCollection
    {
        $items = $this->getItems();
        $items[] = $item;
        return new StringCollection($items);
    }


    /**
     * @param int $index
     * @return null|string
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
     * @link http://php.net/manual/en/iteratoraggregate.getiterator.php
     * @return string[]|\ArrayIterator
     */
    final public function getIterator()
    {
        return new \ArrayIterator($this->getItems());
    }
}
