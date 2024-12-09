<?php

declare(strict_types=1);

namespace Test\Xparse\ElementFinder\Helper;

use PHPUnit\Framework\TestCase;
use Xparse\ElementFinder\Helper\StringHelper;

/**
 * @author Ivan Shcherbak <alotofall@gmail.com>
 */
final class StringHelperTest extends TestCase
{
    public function testEncode(): void
    {
        $data = [
            'AA&lt;<' => 'AA&lt;&#60;',
            '<' => '&#60;',
            '> &#s' => '&#62; &#s',
            '&  ' => '&#38;  ',
            '¢' => '&#162;',
            '£' => '&#163;',
            '¥' => '&#165;',
            '€' => '&#8364;',
            '©' => '&#169;',
            'Data ®' => 'Data &#174;',
        ];

        foreach ($data as $expect => $string) {
            $output = StringHelper::safeEncodeStr($string);
            self::assertEquals($expect, $output);
        }
    }
}
