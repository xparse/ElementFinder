<?php

  namespace Xparse\ElementFinder\Collection;

  /**
   * @author Ivan Shcherbak <dev@funivan.com> 11/7/14
   */
  abstract class TypedCollection extends BaseCollection {


    /**
     * You can add or append only one type of items to this collection
     *
     * @param $item
     * @throws \Exception
     */
    public abstract function validateType($item);


    /**
     * @inheritdoc
     */
    public function prepend($item) {
      $this->validateType($item);
      return parent::prepend($item);
    }

    /**
     * @inheritdoc
     */
    public function append($item) {
      $this->validateType($item);
      return parent::append($item);
    }

    /**
     * @inheritdoc
     */
    public function addAfter($index, $items) {

      if (!is_array($items)) {
        throw new \InvalidArgumentException('You can add after only array of items');
      }

      foreach ($items as $item) {
        $this->validateType($item);
      }

      return parent::addAfter($index, $items);
    }

    /**
     * @inheritdoc
     */
    public function setItems($items) {
      if (!is_array($items)) {
        throw new \InvalidArgumentException('You can set only array of items');
      }

      foreach ($items as $key => $item) {
        $this->validateType($item);
      }

      return parent::setItems($items);
    }

    /**
     * @inheritdoc
     */
    public function offsetSet($offset, $item) {
      $this->validateType($item);
      return parent::offsetSet($offset, $item);
    }

  }