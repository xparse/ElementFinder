<?php

namespace Test\Xparse\ElementFinder\Dummy;

use Xparse\ExpressionTranslator\ExpressionTranslatorInterface;

/**
 * @author Ivan Shcherbak <alotofall@gmail.com>
 */
class ItemsByClassExpressionTranslator implements ExpressionTranslatorInterface
{

    /**
     * Translate expression to xpath
     * Select items only by specific class
     *
     * @param string $expression
     */
    public function convertToXpath($expression): string
    {
        return '//*[@class="' . $expression . '"]';
    }
}
