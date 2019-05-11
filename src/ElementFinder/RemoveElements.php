<?php

declare(strict_types=1);

namespace Xparse\ElementFinder\ElementFinder;

class RemoveElements implements ElementFinderModifierInterface
{

    final public function modify(\DOMNodeList $nodeList): void
    {
        foreach ($nodeList as $node) {
            if ($node instanceof \DOMAttr) {
                $node->ownerElement->removeAttribute($node->name);
            } else {
                $node->parentNode->removeChild($node);
            }
        }
    }
}
