<?php

namespace Test\Xparse\ElementFinder\Dummy;

use Xparse\ExpressionTranslator\ExpressionTranslatorInterface;

/**
 * @author Ivan Shcherbak <alotofall@gmail.com>
 */
class ItemsByClassExpressionTranslator implements ExpressionTranslatorInterface
{

    final public function convertToXpath(string $expression): string
    {
        return '//*[@class="' . $expression . '"]';
    }
}
