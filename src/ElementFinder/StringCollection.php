<?php
  declare(strict_types=1);

  namespace Xparse\ElementFinder\ElementFinder;

  use Xparse\ElementFinder\Helper\RegexHelper;

  /**
   * @author Ivan Shcherbak <dev@funivan.com>
   */
  class StringCollection implements \Iterator, \ArrayAccess, \Countable {

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
    public function count() : int {
      return count($this->items);
    }

    /**
     * Add one item to begin of collection
     * This item is accessible via `$collection->getFirst();`
     *
     * @param mixed $item
     * @return self
     */
    public function prepend($item) : self {
      $this->validateType($item);
      array_unshift($this->items, $item);
      return $this;
    }

    /**
     * Add one item to the end of collection
     * This item is accessible via `$collection->getLast();`
     *
     * @param mixed $item
     * @return self
     */
    public function append($item) : self {
      $this->validateType($item);
      $this->items[] = $item;
      return $this;
    }

    /**
     * @param int $index
     * @param string[] $items
     * @return self
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
     * @param string[] $items
     * @return self
     */
    public function setItems(array $items) : self {

      foreach ($items as $key => $item) {
        $this->validateType($item);
      }
      $this->items = $items;
      $this->rewind();
      return $this;
    }

    /**
     * Remove part of items from collection
     * Works as array_slice
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
     * Rewind current collection
     */
    public function rewind() {
      $this->position = 0;
      $this->items = array_values($this->items);
    }

    /**
     * Return last item from collection
     *
     * @return null|string
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
     * @return null|string
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
     * @return null|string
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
     * @return null|string
     */
    public function getPrevious(int $step = 1) {
      $position = ($this->position - $step);
      return $this->items[$position] ?? null;
    }

    /**
     * Return current item in collection
     *
     * @return null|string
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
     * @return int
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
     * @return bool
     */
    public function valid() : bool {
      return isset($this->items[$this->position]);
    }

    /**
     * Add item to the end or modify item with given key
     *
     * @deprecated
     * @param int|null $offset
     * @param string $item
     * @return self
     */
    public function offsetSet($offset, $item) : self {
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
     * @return null|string
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
     * @return string[]
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
     * @param callable $callback
     * @return self
     */
    public function map(callable $callback) : self {

      foreach ($this->getItems() as $index => $item) {
        call_user_func_array($callback, [$item, $index, $this]);
      }

      $this->rewind();

      return $this;
    }

    /**
     * @deprecated
     * @param int $index
     */
    private function validateIndex($index) {
      if (!is_int($index)) {
        throw new \InvalidArgumentException('Invalid type of index. Must be integer');
      }
    }

    /**
     * You can add or append only one type of items to this collection
     *
     * @param string|float|int $item
     * @return bool
     * @throws \Exception
     */
    private function validateType($item) : bool {
      if (is_string($item) or is_float($item) or is_int($item)) {
        return true;
      }
      throw new \InvalidArgumentException('Expect string');
    }


    /**
     * @param int $index
     * @return string
     */
    public function item(int $index) : string {
      if (isset($this->items[$index])) {
        return $this->items[$index];
      }
      return '';
    }


    /**
     * @param string $regexp
     * @param string $to
     * @return self
     */
    public function replace(string $regexp, string $to = '') : self {
      foreach ($this->items as $index => $item) {
        $this->items[$index] = preg_replace($regexp, $to, $item);
      }

      return $this;
    }


    /**
     * Match strings and return new collection
     * @param string $regexp
     * @param int $index
     * @return StringCollection
     */
    public function match(string $regexp, int $index = 1) : StringCollection {
      return RegexHelper::match($regexp, $index, $this->items);
    }


    /**
     * Split strings by regexp
     * @param string $regexp
     * @return StringCollection
     */
    public function split(string $regexp) : StringCollection {
      $items = new StringCollection();

      foreach ($this->items as $item) {
        $data = preg_split($regexp, $item);
        foreach ($data as $string) {
          $items->append($string);
        }
      }

      return $items;
    }

  }