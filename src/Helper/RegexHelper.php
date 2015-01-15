<?php

  namespace Xparse\ElementFinder\Helper;

  /**
   * @author Ivan Shcherbak <dev@funivan.com> 1/11/15
   */
  class RegexHelper {

    /**
     * @param string $regex
     * @param integer $i
     * @param array $strings
     * @return \Xparse\ElementFinder\ElementFinder\StringCollection
     * @throws \Exception
     */
    public static function match($regex, $i, array $strings) {

      if (!is_numeric($i)) {
        throw new \InvalidArgumentException('Expect integer');
      }

      $items = new \Xparse\ElementFinder\ElementFinder\StringCollection();

      foreach ($strings as $string) {

        preg_match_all($regex, $string, $matchedData);

        if (!isset($matchedData[$i])) {
          continue;
        }

        foreach ($matchedData[$i] as $resultString) {
          $items[] = $resultString;
        }
      }

      return $items;

    }

    /**
     * @param string $regex
     * @param callable $i
     * @param array $strings
     * @return \Xparse\ElementFinder\ElementFinder\StringCollection
     * @throws \Exception
     */
    public static function matchCallback($regex, callable $i, array $strings) {

      $items = new \Xparse\ElementFinder\ElementFinder\StringCollection();

      foreach ($strings as $string) {

        if (preg_match_all($regex, $string, $matchedData)) {

          $rawStringResult = $i($matchedData);

          if (!is_array($rawStringResult)) {
            throw new \Exception("Invalid value. Expect array from callback");
          }

          foreach ($rawStringResult as $resultString) {
            $items[] = $resultString;
          }
        }
      }

      return $items;

    }
  }