<?php

  namespace Xparse\ElementFinder\Collection;

  abstract class ObjectCollection extends TypedCollection {

    /**
     * Used for validation
     * Return class name
     *
     * @codeCoverageIgnore
     * @return string
     */
    public abstract function objectsClassName();

    /**
     * Clone each item
     */
    public function __clone() {
      $items = array();
      foreach ($this->items as $item) {
        $items[] = clone $item;
      }
      $this->setItems($items);
    }


    public function validateType($item) {
      $itemClass = $this->objectsClassName();
      if (($item instanceof $itemClass) === false) {
        $className = ($item === null) ? null : get_class($item);
        throw new \Exception('Invalid object type. Expect ' . $this->objectsClassName() . ' given ' . $className);
      }
    }

  }
