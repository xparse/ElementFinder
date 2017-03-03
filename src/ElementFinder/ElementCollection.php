<?php
  declare(strict_types=1);

  namespace Xparse\ElementFinder\ElementFinder;

  /**
   * @author Ivan Shcherbak <dev@funivan.com>
   */
  class ElementCollection implements \Iterator, \ArrayAccess, \Countable {
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

      if (count($items) > 0) {
        $this->setItems($items);
      }

    }


    /**
     * Return number of items in this collection
     *
     */
    public function count() : int {
      return count($this->items);
    }

    /**
     * Add one item to begin of collection
     * This item is accessible via `$collection->getFirst();`
     *
     */
    public function prepend(Element $item) : self {
      array_unshift($this->items, $item);
      return $this;
    }

    /**
     * Add one item to the end of collection
     * This item is accessible via `$collection->getLast();`
     *
     */
    public function append(Element $item) : self {
      $this->items[] = $item;
      return $this;
    }

    /**
     * @param int $index
     * @param Element[] $items
     * @return $this
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
     * @param Element[] $items
     * @return $this
     */
    public function setItems(array $items) : self {

      foreach ($items as $item) {
        $this->validateType($item);
      }

      $this->items = $items;
      $this->rewind();
      return $this;
    }

    /**
     * Remove part of items from collection
     * Works as array_slice
     *
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
     */
    public function extractItems(int $offset, int $length = null) : self {
      $items = array_slice($this->items, $offset, $length);
      $this->setItems($items);
      return $this;
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
     * @return null|Element
     */
    public function getLast() {
      if ($this->count() === 0) {
        return null;
      }
      return end($this->items);
    }

    /**
     * Return first item from collection
     * @return null|Element
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
     * @return null|Element
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
     * @return null|Element
     */
    public function getPrevious(int $step = 1) {
      $position = ($this->position - $step);
      return $this->items[$position] ?? null;
    }

    /**
     * Return current item in collection
     *
     * @return null|Element
     */
    public function current() {
      if (!isset($this->items[$this->position])) {
        return null;
      }

      return $this->items[$this->position];
    }

    /**
     * Return current position
     *
     */
    public function key() : int {
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
     */
    public function valid() : bool {
      return isset($this->items[$this->position]);
    }

    /**
     * Add item to the end or modify item with given key
     *
     * @deprecated
     * @param int|null $offset
     * @param Element $item
     * @return $this
     */
    public function offsetSet($offset, $item) {
      $this->validateType($item);

      if (null === $offset) {
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
     * @deprecated
     * @param int $offset
     * @return bool
     */
    public function offsetExists($offset) : bool {
      return isset($this->items[$offset]);
    }

    /**
     * Remove item from collection
     *
     * @deprecated
     * @param int $offset
     */
    public function offsetUnset($offset) {
      unset($this->items[$offset]);
    }

    /**
     * Get item from collection
     *
     * @deprecated
     * @param int $offset
     * @return null|Element
     */
    public function offsetGet($offset) {
      return $this->items[$offset] ?? null;
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
     * @return Element[]
     */
    public function getItems() : array {
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
     */
    public function map(callable $callback) : self {

      foreach ($this->getItems() as $index => $item) {
        call_user_func_array($callback, [$item, $index, $this]);
      }

      $this->rewind();

      return $this;
    }

    /**
     * @param int $index
     */
    private function validateIndex($index) {
      if (!is_int($index)) {
        throw new \InvalidArgumentException('Invalid type of index. Must be integer');
      }
    }

    /**
     * @param $item
     * @throws \Exception
     */
    private function validateType($item) {
      $itemClassName = Element::class;
      if (($item instanceof $itemClassName) === false) {
        $className = ($item === null) ? null : get_class($item);
        throw new \Exception('Invalid object type. Expect ' . Element::class . ' given ' . $className);
      }
    }

    /**
     * @param int $index
     * @return null|Element
     */
    public function item(int $index) {
      if (isset($this->items[$index])) {
        return $this->items[$index];
      }
      return null;
    }


    /**
     * Array of all elements attributes
     *
     */
    public function getAttributes() : array {
      $allAttributes = [];
      foreach ($this as $key => $element) {
        $allAttributes[$key] = $element->getAttributes();
      }

      return $allAttributes;
    }

  } 