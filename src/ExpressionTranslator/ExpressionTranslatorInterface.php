<?php

declare(strict_types=1);

namespace Xparse\ElementFinder\ExpressionTranslator;

interface ExpressionTranslatorInterface
{
    /**
     * Translate expression to xpath
     * For example you can use css
     */
    public function convertToXpath(string $expression): string;
}
