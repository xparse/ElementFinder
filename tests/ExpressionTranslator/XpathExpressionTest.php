<?php

declare(strict_types=1);

namespace Test\Xparse\ElementFinder\ExpressionTranslator;

use PHPUnit\Framework\TestCase;
use Xparse\ElementFinder\ExpressionTranslator\XpathExpression;

class XpathExpressionTest extends TestCase
{
    final public function testAssertSameIO(): void
    {
        self::assertSame(
            'custom-expression',
            (new XpathExpression())->convertToXpath('custom-expression')
        );
    }
}
