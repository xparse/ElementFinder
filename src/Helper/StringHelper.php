<?php

declare(strict_types=1);

namespace Xparse\ElementFinder\Helper;

/**
 * @author Ivan Shcherbak <alotofall@gmail.com>
 */
class StringHelper
{
    final public static function safeEncodeStr(string $str): string
    {
        return preg_replace_callback('/&#([a-z\d]+);/i', static function ($m) {
            $value = (string)$m[0];
            $value = mb_convert_encoding($value, 'UTF-8', 'HTML-ENTITIES');
            return $value;
        }, $str);
    }
}
