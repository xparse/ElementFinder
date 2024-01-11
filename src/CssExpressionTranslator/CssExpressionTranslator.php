<?php

declare(strict_types=1);

namespace Xparse\ElementFinder\CssExpressionTranslator;

use Symfony\Component\CssSelector\CssSelectorConverter;
use Xparse\ElementFinder\ExpressionTranslator\ExpressionTranslatorInterface;

/**
 * @author Ivan Shcherbak <alotofall@gmail.com>
 */
class CssExpressionTranslator extends CssSelectorConverter implements ExpressionTranslatorInterface
{

    public function convertToXpath(string $expression): string
    {
        $xpathExpression = [];
        foreach (explode(', ', $expression) as $part) {
            preg_match('!(.+) (@.+|.+\(\))$!', $part, $matchExpression);
            if (!array_key_exists(2, $matchExpression)) {
                $xpathExpression[] = $this->toXPath($part);
            } else {
                $xpathExpression[] = $this->toXPath($matchExpression[1]) . '/' . $matchExpression[2];
            }
        }
        return implode(' | ', $xpathExpression);
    }


}