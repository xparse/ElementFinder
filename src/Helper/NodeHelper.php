<?php

  namespace Xparse\ElementFinder\Helper;

  /**            
   * @access private
   * 
   * @author Ivan Shcherbak <dev@funivan.com> 03.12.15
   */
  class NodeHelper {

    /**
     * @param \DOMNode $node
     * @return string
     */
    public static function getOuterHtml(\DOMNode $node) {

      $domDocument = new \DOMDocument('1.0');
      $b = $domDocument->importNode($node->cloneNode(true), true);
      $domDocument->appendChild($b);

      $html = $domDocument->saveHtml();
      $html = StringHelper::safeEncodeStr($html);

      return $html;
    }


    /**
     * @param \DOMNode $itemObj
     * @return string
     */
    public static function getInnerHtml(\DOMNode $itemObj) {
      $innerHtml = '';
      $children = $itemObj->childNodes;
      /** @var \DOMNode $child */
      foreach ($children as $child) {
        $innerHtml .= $child->ownerDocument->saveXML($child);
      }
      $innerHtml = StringHelper::safeEncodeStr($innerHtml);
      return $innerHtml;
    }
  }