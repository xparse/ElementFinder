<?php

declare(strict_types=1);

namespace Xparse\ElementFinder\Helper;

/**
 * @author Ivan Shcherbak <alotofall@gmail.com>
 */
class NodeHelper
{

    /**
     * @param \DOMNode $node
     * @return string
     */
    final public static function getOuterContent(\DOMNode $node): string
    {
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
    final public static function getInnerContent(\DOMNode $itemObj): string
    {
        $content = '';
        foreach ($itemObj->childNodes as $child) {
            $content .= $child->ownerDocument->saveXML($child);
        }
        return StringHelper::safeEncodeStr($content);
    }
}
