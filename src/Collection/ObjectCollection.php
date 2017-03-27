<?php

  declare(strict_types=1);

  namespace Xparse\ElementFinder\Collection;

  use Xparse\ElementFinder\ElementFinder;

  /**
   * @author Ivan Shcherbak <alotofall@gmail.com>
   */
  class ObjectCollection implements \IteratorAggregate, \Countable {

    /**
     * @var int
     */
    protected $position = 0;

    /**
     * Array of objects
     *
     * @var ElementFinder[]
     */
    protected $items = [];


    /**
     * @param ElementFinder[] $items
     * @throws \Exception
     */
    public function __construct(array $items = []) {
      $this->setItems($items);
    }


    /**
     * Clone each item
     */
    public function __clone() {
      $items = [];
      foreach ($this->items as $item) {
        $items[] = clone $item;
      }
      $this->setItems($items);
    }


    /**
     * Return number of items in this collection
     *
     * @return int
     */
    public function count() : int {
      return count($this->items);
    }


    /**
     * Add one item to begin of collection
     * This item is accessible via `$collection->getFirst();`
     *
     * @param ElementFinder $item
     * @return self
     */
    public function prepend(ElementFinder $item) : self {
      array_unshift($this->items, $item);
      return $this;
    }


    /**
     * Add one item to the end of collection
     * This item is accessible via `$collection->getLast();`
     *
     * @param ElementFinder $item
     * @return self
     */
    public function append(ElementFinder $item) : self {
      $this->items[] = $item;
      return $this;
    }


    /**
     * @param int $index
     * @param ElementFinder[] $items
     * @return self
     * @throws \Exception
     */
    public function addAfter(int $index, array $items) : self {

      foreach ($items as $item) {
        $this->validateType($item);
      }

      $offset = $index + 1;
      $firstPart = array_slice($this->items, 0, $offset);
      $secondPart = array_slice($this->items, $offset);
      $this->items = array_merge($firstPart, $items, $secondPart);
      return $this;
    }


    /**
     * Truncate current list of items and add new
     *
     * @param ElementFinder[] $items
     * @return self
     * @throws \Exception
     */
    public function setItems(array $items) : self {
      foreach ($items as $key => $item) {
        $this->validateType($item);
      }
      $this->items = $items;
      return $this;
    }


    /**
     * Remove part of items from collection
     * Works as array_slice
     *
     * @param int $offset
     * @param int|null $length
     * @return self
     */
    public function slice(int $offset, int $length = null) : self {
      $this->items = array_slice($this->items, $offset, $length);
      return $this;
    }


    /**
     * Take part of items and return new collection
     * Works as array_slice
     * At this point items in 2 collection is same
     *
     * @param int $offset
     * @param int|null $length
     * @return self
     */
    public function extractItems(int $offset, int $length = null) : self {
      $items = array_slice($this->items, $offset, $length);
      $this->setItems($items);
      return $this;
    }


    /**
     * Return last item from collection
     *
     * @return null|ElementFinder
     */
    public function getLast() {
      if ($this->count() === 0) {
        return null;
      }
      return end($this->items);
    }


    /**
     * Return first item from collection
     *
     * @return null|ElementFinder
     */
    public function getFirst() {
      if ($this->count() === 0) {
        return null;
      }
      return reset($this->items);
    }


    /**
     * Return next item from current
     * Also can return item with position from current + $step
     *
     * @param int $step
     * @return null|ElementFinder
     */
    public function getNext(int $step = 1) {
      $position = ($this->position + $step);
      return $this->items[$position] ?? null;
    }


    /**
     * Return previous item
     * Also can return previous from current position + $step
     *
     * @param int $step
     * @return null|ElementFinder
     */
    public function getPrevious(int $step = 1) {
      $position = ($this->position - $step);
      return $this->items[$position] ?? null;
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
     * @return ElementFinder[]
     */
    public function getItems() : array {
      return $this->items;
    }


    /**
     * Iterate over objects in collection
     *
     * <code>
     * $collection->walk(function(ElementFinder $item, int $index, ObjectCollection $collection){
     *    print_r($item->content('//a')->getItems());
     * })
     * </code>
     * @param callable $callback
     * @return self
     */
    public function walk(callable $callback) : self {
      foreach ($this->getItems() as $index => $item) {
        $callback($item, $index, $this);
      }
      return $this;
    }


    /**
     * @deprecated
     * @see walk
     *
     * @param callable $callback
     * @return self
     */
    public function map(callable $callback) : self {
      $this->walk($callback);
      return $this;
    }


    /**
     * @param int $index
     * @return null|ElementFinder
     */
    public function item(int $index) {
      if (isset($this->items[$index])) {
        return $this->items[$index];
      }
      return null;
    }


    /**
     * @param string $regexp
     * @param string $to
     * @return self
     * @throws \Exception
     */
    public function replace(string $regexp, string $to = '') : self {
      foreach ($this as $item) {
        $item->replace($regexp, $to);
      }
      return $this;
    }


    /**
     * @deprecated
     * @param $item
     * @throws \Exception
     */
    private function validateType($item) {
      $itemClassName = ElementFinder::class;
      if (($item instanceof $itemClassName) === false) {
        $className = ($item === null) ? null : get_class($item);
        throw new \Exception('Invalid object type. Expect ' . ElementFinder::class . ' given ' . $className);
      }
    }


    public function merge(ObjectCollection $collection) : ObjectCollection {
      return new ObjectCollection(array_merge($this->getItems(), $collection->getItems()));
    }


    /**
     * Retrieve an external iterator
     *
     * @link http://php.net/manual/en/iteratoraggregate.getiterator.php
     * @return ElementFinder[]|\ArrayIterator An instance of an object implementing Iterator or Traversable
     */
    public function getIterator() {
      return new \ArrayIterator($this->items);
    }

  }