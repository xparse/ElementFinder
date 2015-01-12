<?php

  namespace Xparse\ElementFinder\ElementFinder;

  /**
   * @author Ivan Shcherbak <dev@funivan.com> 6/3/14
   * @method \Xparse\ElementFinder\ElementFinder\Element offsetGet($offset);
   */
  class ElementCollection extends \Fiv\Collection\ObjectCollection {

    /**
     * @inheritdoc
     */
    public function objectsClassName() {
      return '\Xparse\ElementFinder\ElementFinder\Element';
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
      $allAttributes = array();
      foreach ($this as $key => $element) {
        $allAttributes[$key] = array();
        $allAttributes[$key] = $element->getAttributes();
      }

      return $allAttributes;
    }

  } 