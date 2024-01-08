<?php

declare(strict_types=1);

namespace Xparse\ElementFinder\DomNodeListAction;

use DOMNodeList;
interface DomNodeListActionInterface
{
    public function execute(DOMNodeList $nodeList): void;
}
