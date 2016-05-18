<?php

  namespace Xparse\ElementFinder\ElementFinder;

  /**
   * @author Ivan Shcherbak <dev@funivan.com>
   */
  class Element extends \DOMElement {

    /**
     * Array of element attributes
     *
     * @return array
     */
    public function getAttributes() {
      $attributes = [];
      foreach ($this->attributes as $attr) {
        $attributes[$attr->name] = $attr->value;
      }

      return $attributes;
    }

  }