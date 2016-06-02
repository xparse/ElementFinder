<?php

  namespace Xparse\ElementFinder\Helper;

  /**
   *
   * @author Ivan Shcherbak <dev@funivan.com>
   */
  class NodeHelper {

    /**
     * @param \DOMNode $node
     * @return string
     */
    public static function getOuterContent(\DOMNode $node) {

      $domDocument = new \DOMDocument('1.0');
      $b = $domDocument->importNode($node->cloneNode(true), true);
      $domDocument->appendChild($b);

      $content = $domDocument->saveHTML();
      $content = StringHelper::safeEncodeStr($content);

      return $content;
    }


    /**
     * @param \DOMNode $itemObj
     * @return string
     */
    public static function getInnerContent(\DOMNode $itemObj) {
      $innerContent = '';
      $children = $itemObj->childNodes;
      /** @var \DOMNode $child */
      foreach ($children as $child) {
        $innerContent .= $child->ownerDocument->saveXML($child);
      }
      $innerContent = StringHelper::safeEncodeStr($innerContent);
      return $innerContent;
    }

  }