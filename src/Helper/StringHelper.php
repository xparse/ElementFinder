<?php

  namespace Xparse\ElementFinder\Helper;

  /**
   * @author Ivan Shcherbak <dev@funivan.com>
   */
  class StringHelper {

    /**
     * Simple helper function for str encoding
     *
     * @param string $str
     * @return string
     */
    public static function safeEncodeStr($str) {
      return preg_replace_callback('/&#([a-z\d]+);/i', function ($m) {
        $value = (string) $m[0];
        $value = mb_convert_encoding($value, 'UTF-8', 'HTML-ENTITIES');
        return $value;
      }, $str);
    }
  }