<?php

  declare(strict_types=1);

  namespace Xparse\ElementFinder\Collection;

  use Xparse\ElementFinder\ElementFinder\Element;

  /**
   * @author Ivan Shcherbak <alotofall@gmail.com>
   */
  class ElementCollection implements \IteratorAggregate, \Countable {

    /**
     * @var int
     */
    protected $position = 0;

    /**
     * Array of objects
     *
     * @var Element[]
     */
    protected $items = [];


    /**
     * @param Element[] $items
     */
    public function __construct(array $items = []) {

      if (count($items) > 0) {
        $this->setItems($items);
      }

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
     * Truncate current list of items and add new
     *
     * @param Element[] $items
     * @return self
     * @throws \Exception
     */
    public function setItems(array $items) : self {

      foreach ($items as $item) {
        $this->validateType($item);
      }

      $this->items = $items;
      return $this;
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
     * @return Element[]
     */
    public function getItems() : array {
      return $this->items;
    }


    /**
     * Iterate over objects in collection
     *
     * <code>
     * $collection->walk(function(Element $item, int $index, ElementCollection $collection){
     *    echo $item->nodeValue;
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
     * @return array
     */
    public function getAttributes() : array {
      $allAttributes = [];
      foreach ($this->items as $key => $element) {
        $allAttributes[$key] = $element->getAttributes();
      }

      return $allAttributes;
    }


    public function merge(ElementCollection $collection) : ElementCollection {
      return new ElementCollection(array_merge($this->getItems(), $collection->getItems()));
    }


    /**
     * Retrieve an external iterator
     *
     * @link http://php.net/manual/en/iteratoraggregate.getiterator.php
     * @return Element[]|\ArrayIterator An instance of an object implementing Iterator or Traversable
     */
    public function getIterator() {
      return new \ArrayIterator($this->items);
    }

  }