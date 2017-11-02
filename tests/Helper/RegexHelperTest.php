<?php

declare(strict_types=1);

namespace Test\Xparse\ElementFinder\Helper;

use PHPUnit\Framework\TestCase;

/**
 * @author Ivan Shcherbak <alotofall@gmail.com>
 */
class RegexHelperTest extends TestCase
{
    public function testInvalidRegexForCallback()
    {
        $items = \Xparse\ElementFinder\Helper\RegexHelper::matchCallback('![a-z]!', function () {
            return [];
        }, ['1']);
        self::assertCount(0, $items);
    }
}
