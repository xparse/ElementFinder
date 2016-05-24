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
     * @deprecated Use NodeHelper::getOuterContent instead
     * @param \DOMNode $node
     * @return string
     */
    public static function getOuterHtml(\DOMNode $node) {
      trigger_error('Deprecated', E_USER_DEPRECATED);
      $domDocument = new \DOMDocument('1.0');
      $b = $domDocument->importNode($node->cloneNode(true), true);
      $domDocument->appendChild($b);

      $html = $domDocument->saveHTML();
      $html = StringHelper::safeEncodeStr($html);

      return $html;
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


    /**
     * @deprecated Use NodeHelper::getInnerContent instead
     * @param \DOMNode $itemObj
     * @return string
     */
    public static function getInnerHtml(\DOMNode $itemObj) {
      trigger_error('Deprecated', E_USER_DEPRECATED);
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