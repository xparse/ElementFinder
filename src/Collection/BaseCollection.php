<?php

  namespace Xparse\ElementFinder\Collection;

  /**
   *
   * @package Fiv\Spl
   */
  class BaseCollection implements \Iterator, \ArrayAccess, \Countable {

    /**
     * @var int
     */
    protected $position = 0;

    /**
     * Array of objects
     *
     * @var array
     */
    protected $items = [];

    /**
     * @param array $items
     */
    public function __construct(array $items = []) {

      if (!empty($items)) {
        $this->setItems($items);
      }

    }

    /**
     *
     */
    public function __clone() {
      $items = [];
      foreach ($this->items as $item) {
        $items[] = $item;
      }
      $this->setItems($items);
    }

    /**
     * Return number of items in this collection
     *
     * @return int
     */
    public function count() {
      return count($this->items);
    }

    /**
     * Add one item to begin of collection
     * This item is accessible via `$collection->getFirst();`
     *
     * @param $item
     * @return $this
     */
    public function prepend($item) {
      array_unshift($this->items, $item);
      return $this;
    }

    /**
     * Add one item to the end of collection
     * This item is accessible via `$collection->getLast();`
     *
     * @param $item
     * @return $this
     */
    public function append($item) {
      $this->items[] = $item;
      return $this;
    }

    /**
     * @param int $index
     * @param array $items
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function addAfter($index, $items) {
      if (!is_array($items)) {
        throw new \InvalidArgumentException('You can add after only array of items');
      }

      $this->validateIndex($index);

      $offset = $index + 1;
      $firstPart = array_slice($this->items, 0, $offset);
      $secondPart = array_slice($this->items, $offset);
      $this->items = array_merge($firstPart, $items, $secondPart);
      return $this;
    }

    /**
     * Truncate current list of items and add new
     *
     * @param array $items
     * @return $this
     */
    public function setItems($items) {

      if (!is_array($items)) {
        throw new \InvalidArgumentException('You can set only array of items');
      }

      $this->items = $items;
      $this->rewind();
      return $this;
    }

    /**
     * Remove part of items from collection
     * Works as array_slice
     *
     *
     * @param $offset
     * @param null $length
     * @return $this
     */
    public function slice($offset, $length = null) {
      $this->items = array_slice($this->items, $offset, $length);
      return $this;
    }

    /**
     * Take part of items and return new collection
     * Works as array_slice
     * At this point items in 2 collection is same
     *
     * @param int $offset
     * @param null $length
     * @return self
     */
    public function extractItems($offset, $length = null) {
      $items = array_slice($this->items, $offset, $length);
      $className = get_called_class();
      $collection = new $className();
      /** @var BaseCollection $collection */
      $collection->setItems($items);
      return $collection;
    }

    /**
     * Rewind current collection
     */
    public function rewind() {
      $this->position = 0;
      $this->items = array_values($this->items);
    }

    /**
     * Return last item from collection
     *
     * @return mixed
     */
    public function getLast() {
      return end($this->items);
    }

    /**
     * Return first item from collection
     * @return mixed
     */
    public function getFirst() {
      return reset($this->items);
    }

    /**
     * Return next item from current
     * Also can return item with position from current + $step
     *
     * @param int $step
     * @return mixed
     */
    public function getNext($step = 1) {
      $position = ($this->position + $step);
      return isset($this->items[$position]) ? $this->items[$position] : null;
    }

    /**
     * Return previous item
     * Also can return previous from current position + $step
     *
     * @param int $step
     * @return mixed
     */
    public function getPrevious($step = 1) {
      $position = ($this->position - $step);
      return isset($this->items[$position]) ? $this->items[$position] : null;
    }

    /**
     * Return current item in collection
     *
     * @return object
     */
    public function current() {
      return $this->items[$this->position];
    }

    /**
     * Return current position
     *
     * @return int
     */
    public function key() {
      return $this->position;
    }

    /**
     * Switch to next position
     */
    public function next() {
      ++$this->position;
    }

    /**
     * Check if item exist in current position
     *
     * @return bool
     */
    public function valid() {
      return isset($this->items[$this->position]);
    }

    /**
     * Add item to the end or modify item with given key
     *
     * @param int|null $offset
     * @param object $item
     * @return $this
     */
    public function offsetSet($offset, $item) {

      if (is_null($offset)) {
        $this->append($item);
      } else {
        $this->validateIndex($offset);
        $this->items[$offset] = $item;
      }

      return $this;
    }

    /**
     * Check if item with given offset exists
     *
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists($offset) {
      return isset($this->items[$offset]);
    }

    /**
     * Remove item from collection
     *
     * @param int $offset
     */
    public function offsetUnset($offset) {
      unset($this->items[$offset]);
    }

    /**
     * Get item from collection
     *
     * @param int $offset
     * @return object
     */
    public function offsetGet($offset) {
      return isset($this->items[$offset]) ? $this->items[$offset] : null;
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
     * @return object[]
     */
    public function getItems() {
      return $this->items;
    }

    /**
     * Iterate over objects in collection
     *
     * <code>
     * $collection->map(function($item, $index, $collection){
     *    if ( $index > 0 ) {
     *      $item->remove();
     *    }
     * })
     * </code>
     *
     * @param callback $callback
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function map($callback) {

      if (!is_callable($callback)) {
        throw new \InvalidArgumentException('Invalid callback function');
      }

      foreach ($this->getItems() as $index => $item) {
        call_user_func_array($callback, [$item, $index, $this]);
      }

      $this->rewind();

      return $this;
    }

    /**
     * @param int $index
     */
    protected function validateIndex($index) {
      if (!is_int($index)) {
        throw new \InvalidArgumentException('Invalid type of index. Must be integer');
      }
    }

  }