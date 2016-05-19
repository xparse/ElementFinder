<?php

  namespace Xparse\ElementFinder\ElementFinder;

  use Fiv\Collection\TypedCollection;
  use Xparse\ElementFinder\Helper\RegexHelper;

  /**
   * @author Ivan Shcherbak <dev@funivan.com>
   * @method string offsetGet($offset)
   * @method string current()
   * @method string getFirst()
   * @method string getLast()
   * @method string getPrevious($step)
   * @method string getNext($step)
   * @method string[] getItems()
   */
  class StringCollection extends TypedCollection {

    /**
     * You can add or append only one type of items to this collection
     *
     * @param string $item
     * @return bool
     * @throws \Exception
     */
    public function validateType($item) {
      if (is_string($item) or is_float($item) or is_int($item)) {
        return true;
      }
      throw new \InvalidArgumentException("Expect string");
    }


    /**
     * @param int $index
     * @return string
     */
    public function item($index) {
      if (isset($this->items[$index])) {
        return $this->items[$index];
      } else {
        return "";
      }
    }


    /**
     * @param string $regexp
     * @param string $to
     * @return $this
     */
    public function replace($regexp, $to = '') {
      foreach ($this->items as $index => $item) {
        $this->items[$index] = preg_replace($regexp, $to, $item);
      }

      return $this;
    }


    /**
     * Match strings and return new collection
     *
     * @param string $regexp
     * @param int $index
     * @return StringCollection
     */
    public function match($regexp, $index = 1) {
      return RegexHelper::match($regexp, $index, $this->items);
    }


    /**
     * Split strings by regexp
     *
     * @param string $regexp
     * @return StringCollection
     */
    public function split($regexp) {
      $items = new StringCollection();

      foreach ($this->items as $item) {
        $data = preg_split($regexp, $item);
        foreach ($data as $string) {
          $items[] = $string;
        }
      }

      return $items;
    }

  } 