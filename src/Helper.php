<?php

  namespace Xparse\ElementFinder;

  /**
   * @author Ivan Shcherbak <dev@funivan.com> 6/3/14
   */
  class Helper {

    /**
     * @param \DOMElement $node
     * @param bool $isHtml
     * @return string
     */
    public static function getOuterHtml( $node, $isHtml = true) {

      if ($isHtml) {
        $saveMethod = 'saveHtml';
      } else {
        $saveMethod = 'saveXml';
      }

      $domDocument = new \DOMDocument('1.0');
      $b = $domDocument->importNode($node->cloneNode(true), true);
      $domDocument->appendChild($b);

      $html = $domDocument->$saveMethod();
      $html = static::safeEncodeStr($html);

      return $html;
    }


    /**
     * @param \DOMElement $itemObj
     * @return string
     */
    public static function getInnerHtml( $itemObj) {
      $innerHtml = '';
      $children = $itemObj->childNodes;
      foreach ($children as $child) {
        $innerHtml .= $child->ownerDocument->saveXML($child);
      }
      $innerHtml = static::safeEncodeStr($innerHtml);
      return $innerHtml;
    }

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