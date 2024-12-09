<?php

declare(strict_types=1);

namespace Test\Xparse\ElementFinder\Dummy;

use Xparse\ElementFinder\ExpressionTranslator\ExpressionTranslatorInterface;

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
