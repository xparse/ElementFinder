<?php
declare(strict_types=1);

namespace Xparse\ElementFinder\DomNodeListAction;

interface DomNodeListActionInterface
{
    public function execute(\DOMNodeList $nodeList): void;
}
