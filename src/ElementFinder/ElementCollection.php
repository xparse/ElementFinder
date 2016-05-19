<?php

  namespace Xparse\ElementFinder\ElementFinder;

  /**
   * @author Ivan Shcherbak <dev@funivan.com>
   * @method Element offsetGet($offset)
   * @method Element current()
   * @method Element getFirst()
   * @method Element getLast()
   * @method Element getPrevious($step)
   * @method Element getNext($step)
   * @method Element[] getItems()
   */
  class ElementCollection extends \Fiv\Collection\ObjectCollection {

    /**
     * @inheritdoc
     */
    public function objectsClassName() {
      return Element::class;
    }


    /**
     * @param int $index
     * @return null|Element
     */
    public function item($index) {
      if (isset($this->items[$index])) {
        return $this->items[$index];
      } else {
        return null;
      }
    }


    /**
     * Array of all elements attributes
     *
     * @return array
     */
    public function getAttributes() {
      $allAttributes = [];
      foreach ($this as $key => $element) {
        $allAttributes[$key] = [];
        $allAttributes[$key] = $element->getAttributes();
      }

      return $allAttributes;
    }

  } 