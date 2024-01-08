<?php

namespace Xparse\ElementFinder\ExpressionTranslator;

/**
 * @author Ivan Shcherbak <alotofall@gmail.com>
 */
class XpathExpression implements ExpressionTranslatorInterface
{

    final public function convertToXpath(string $expression): string
    {
        return $expression;
    }

}