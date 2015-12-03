<?php

  namespace Xparse\ElementFinder\Helper;

  /**
   * @author Ivan Shcherbak <dev@funivan.com> 03.12.15
   */
  class StringHelper {

    /**
     * Simple helper function for str encoding
     *
     * @param string $str
     * @return string
     */
    public static function safeEncodeStr($str) {
      return preg_replace_callback("/&#([a-z\d]+);/i", function ($m) {
        $m[0] = (string) $m[0];
        $m[0] = mb_convert_encoding($m[0], "UTF-8", "HTML-ENTITIES");
        return $m[0];
      }, $str);
    }
  }