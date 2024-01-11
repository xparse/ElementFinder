<?php

declare(strict_types=1);

namespace Xparse\ElementFinder\CssExpressionTranslator;

use InvalidArgumentException;
use Xparse\ElementFinder\ExpressionTranslator\ExpressionTranslatorInterface;

/**
 * Automatically detect xpath or css query language and convert it to the xpath
 *
 * @author Ivan Shcherbak <alotofall@gmail.com>
 */
class CssOrXpathExpressionTranslator implements ExpressionTranslatorInterface
{
    public function __construct(
        private readonly ExpressionTranslatorInterface $cssTranslator = new CssExpressionTranslator()
    ) {
    }

    /**
     * @throws InvalidArgumentException
     */
    final public function convertToXpath(string $expression): string
    {
        $expression = trim($expression);
        if ($expression === '') {
            throw new InvalidArgumentException('Expect not empty expression');
        }
        if ($expression === '.') {
            return $expression;
        }
        if (mb_strpos($expression, './') === 0) {
            return $expression;
        }
        $firstChar = mb_substr($expression, 0, 1);
        if (in_array($firstChar, ['/', '('])) {
            return $expression;
        }
        return $this->cssTranslator->convertToXpath($expression);
    }
}
