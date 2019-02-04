<?php

declare(strict_types=1);

namespace Test\Xparse\ElementFinder\Helper;

use PHPUnit\Framework\TestCase;
use Xparse\ElementFinder\Helper\RegexHelper;

/**
 * @author Ivan Shcherbak <alotofall@gmail.com>
 */
final class RegexHelperTest extends TestCase
{
    public function testInvalidRegexForCallback()
    {
        $items = RegexHelper::matchCallback('![a-z]!', function () {
            return [];
        }, ['1']);
        self::assertCount(0, $items);
    }
}
