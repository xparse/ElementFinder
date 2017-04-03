<?php

  declare(strict_types=1);

  namespace Xparse\ElementFinder\Collection;

  use Xparse\ElementFinder\Helper\RegexHelper;

  /**
   * @author Ivan Shcherbak <alotofall@gmail.com>
   */
  class StringCollection implements \IteratorAggregate, \Countable {

    /**
     * @var int
     */
    protected $position = 0;

    /**
     * Array of strings
     *
     * @var string[]
     */
    protected $items = [];


    /**
     * @param string[] $items
     * @throws \Exception
     */
    public function __construct(array $items = []) {
      foreach ($items as $key => $item) {
        if (is_float($item) or is_int($item)) {
          $item = (string) $item;
          trigger_error('Invalid type. Expect string given ' . gettype($item), E_USER_DEPRECATED);
        } elseif (!is_string($item)) {
          throw new \InvalidArgumentException('Expect string');
        }
      }
      $this->items = array_values($items);
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
     * $collection->walk(function(string $item, int $index, StringCollection $collection){
     *    echo $item;
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
    public function replace(string $regexp, string $to = '') : StringCollection {
      $result = [];
      foreach ($this->items as $index => $item) {
        $result[] = preg_replace($regexp, $to, $item);
      }

      return new StringCollection($result);
    }


    /**
     * Match strings and return new collection
     * @param string $regexp
     * @param int $index
     * @return StringCollection
     * @throws \Exception
     */
    public function match(string $regexp, int $index = 1) : StringCollection {
      return RegexHelper::match($regexp, $index, $this->items);
    }


    /**
     * Split strings by regexp
     *
     * @param string $regexp
     * @return StringCollection
     * @throws \Exception
     */
    public function split(string $regexp) : StringCollection {

      $items = [];
      foreach ($this->items as $item) {
        $data = preg_split($regexp, $item);
        foreach ($data as $string) {
          $items[] = $string;
        }
      }

      return new StringCollection($items);
    }


    /**
     * @return StringCollection
     */
    public function unique() : StringCollection {
      return new StringCollection(array_unique($this->items));
    }


    public final function merge(StringCollection $collection) : StringCollection {
      return new StringCollection(array_merge($this->getItems(), $collection->getItems()));
    }


    public function add(string $item) : StringCollection {
      $items = $this->getItems();
      $items[] = $item;
      return new StringCollection($items);
    }


    /**
     * @param int $index
     * @return null|string
     */
    public function get(int $index) {
      if (array_key_exists($index, $this->items)) {
        return $this->items[$index];
      }
      return null;
    }


    /**
     * Retrieve an external iterator
     *
     * @link http://php.net/manual/en/iteratoraggregate.getiterator.php
     * @return string[]|\ArrayIterator An instance of an object implementing Iterator or Traversable
     */
    public function getIterator() {
      return new \ArrayIterator($this->items);
    }

  }