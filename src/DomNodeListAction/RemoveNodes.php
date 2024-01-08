<?php

declare(strict_types=1);

namespace Xparse\ElementFinder\DomNodeListAction;

use DOMNodeList;
use DOMAttr;
class RemoveNodes implements DomNodeListActionInterface
{
    final public function execute(DOMNodeList $nodeList): void
    {
        foreach ($nodeList as $node) {
            if ($node instanceof DOMAttr) {
                $node->ownerElement->removeAttribute($node->name);
            } else {
                $node->parentNode->removeChild($node);
            }
        }
    }
}
