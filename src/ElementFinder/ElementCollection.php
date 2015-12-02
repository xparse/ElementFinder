<?php

  namespace Xparse\ElementFinder\ElementFinder;

  use Fiv\Collection\ObjectCollection;

  /**
   * @author Ivan Shcherbak <dev@funivan.com> 6/3/14
   * @method Element offsetGet($offset);
   */
  class ElementCollection extends ObjectCollection {

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