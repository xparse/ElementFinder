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
        return count($this->all());
    }


    /**
     * @return null|string
     */
    final public function last()
    {
        $items = $this->all();
        if (count($items) === 0) {
            return null;
        }
        return end($items);
    }

    /**
     * @return null|string
     */
    final public function first()
    {
        $items = $this->all();
        if (count($items) === 0) {
            return null;
        }
        return reset($items);
    }

    /**
     * @return string[]
     */
    final public function all(): array
    {
        return $this->items;
    }


    final public function map(StringModifyInterface $modifier): StringCollection
    {
        $items = [];
        foreach ($this->all() as $item) {
            $items[] = $modifier->modify($item);
        }
        return new StringCollection($items);
    }


    final public function filter(StringFilterInterface $filter): StringCollection
    {
        $items = [];
        foreach ($this->all() as $item) {
            if ($filter->valid($item)) {
                $items[] = $item;
            }
        }
        return new StringCollection($items);
    }


    final public function replace(string $regexp, string $to): StringCollection
    {
        $result = [];
        foreach ($this->all() as $index => $item) {
            $result[] = preg_replace($regexp, $to, $item);
        }
        return new StringCollection($result);
    }


    final public function match(string $regexp, int $index = 1): StringCollection
    {
        return RegexHelper::match($regexp, $index, $this->all());
    }


    final public function split(string $regexp): StringCollection
    {
        $items = [];
        foreach ($this->all() as $item) {
            $data = preg_split($regexp, $item);
            foreach ($data as $string) {
                $items[] = $string;
            }
        }
        return new StringCollection($items);
    }


    final public function unique(): StringCollection
    {
        return new StringCollection(array_unique($this->all()));
    }


    final public function merge(StringCollection $collection): StringCollection
    {
        return new StringCollection(array_merge($this->all(), $collection->all()));
    }


    final public function add(string $item): StringCollection
    {
        $items = $this->all();
        $items[] = $item;
        return new StringCollection($items);
    }


    /**
     * @param int $index
     * @return null|string
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
     * @return string[]|\ArrayIterator
     */
    final public function getIterator()
    {
        return new \ArrayIterator($this->all());
    }
}
