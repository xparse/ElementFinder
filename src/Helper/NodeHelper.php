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

      $html = $domDocument->saveHTML();
      $html = StringHelper::safeEncodeStr($html);

      return $html;
    }


    /**
     * @param \DOMNode $node
     * @return string
     */
    public static function getInnerContent(\DOMNode $node) {
      $innerHtml = '';
      $children = $node->childNodes;
      /** @var \DOMNode $child */
      foreach ($children as $child) {
        $innerHtml .= $child->ownerDocument->saveXML($child);
      }
      $innerHtml = StringHelper::safeEncodeStr($innerHtml);
      return $innerHtml;
    }
  }