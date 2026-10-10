<?php

declare(strict_types=1);

namespace Xparse\ElementFinder\ElementFinder;

use DOMElement;

class Element extends DOMElement
{
    /**
     * @return array Array<String, String>
     */
    final public function getAttributes(): array
    {
        $attributes = [];
        foreach ($this->attributes as $attr) {
            $attributes[$attr->name] = $attr->value;
        }
        return $attributes;
    }
}
