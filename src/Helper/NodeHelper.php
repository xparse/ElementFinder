<?php

declare(strict_types=1);

namespace Xparse\ElementFinder\Helper;

use DOMNode;
use DOMDocument;
use Xparse\ElementFinder\ElementFinder;

/**
 * @author Ivan Shcherbak <alotofall@gmail.com>
 */
class NodeHelper
{
    final public static function getOuterContent(DOMNode $node, int $documentType): string
    {
        $domDocument = new DOMDocument('1.0');
        $b = $domDocument->importNode($node->cloneNode(true), true);
        /** @noinspection UnusedFunctionResultInspection */
        $domDocument->appendChild($b);

        $content = $documentType === ElementFinder::DOCUMENT_XML ? $domDocument->saveXml() : $domDocument->saveHTML();
        $content = StringHelper::safeEncodeStr($content);

        return $content;
    }


    final public static function getInnerContent(DOMNode $itemObj, int $documentType): string
    {
        $content = '';
        foreach ($itemObj->childNodes as $child) {
            $content .= ($documentType === ElementFinder::DOCUMENT_XML ? $child->ownerDocument->saveXml($child) : $child->ownerDocument->saveHTML($child));
        }
        return StringHelper::safeEncodeStr($content);
    }
}
