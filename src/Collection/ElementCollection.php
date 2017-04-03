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
     * @throws \InvalidArgumentException
     */
    public function __construct(array $items = []) {

      /** @noinspection ForeachSourceInspection */
      foreach ($items as $item) {
        if (!$item instanceof Element) {
          $className = ($item === null) ? gettype($item) : get_class($item);
          throw new \InvalidArgumentException('Invalid object type. Expect ' . Element::class . ' given ' . $className);
        }
      }

      $this->items = $items;
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


    /**
     * @param ElementCollection $collection
     * @return ElementCollection
     * @throws \InvalidArgumentException
     */
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