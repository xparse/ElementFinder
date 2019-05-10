<?php

declare(strict_types=1);

namespace Xparse\ElementFinder\Helper;

use Xparse\ElementFinder\Collection\StringCollection;

/**
 * @author Ivan Shcherbak <alotofall@gmail.com>
 * @deprecated Internal class. Will be removed in next release
 */
class RegexHelper
{

    /**
     * @param string[] $strings
     * @throws \Exception
     * @deprecated
     */
    final public static function match(string $regex, int $i, array $strings): StringCollection
    {
        trigger_error('Deprecated. This method is internal', E_USER_DEPRECATED);
        $result = [];
        foreach ($strings as $string) {
            preg_match_all($regex, $string, $matchedData);
            if (!isset($matchedData[$i])) {
                continue;
            }
            foreach ((array)$matchedData[$i] as $resultString) {
                $result[] = $resultString;
            }
        }
        return new StringCollection($result);
    }


    /**
     * @throws \Exception
     */
    final public static function matchCallback(string $regex, callable $i, array $strings): StringCollection
    {
        $result = [];
        foreach ($strings as $string) {
            if (preg_match_all($regex, $string, $matchedData)) {
                $rawStringResult = $i($matchedData);
                if (!is_array($rawStringResult)) {
                    throw new \Exception('Invalid value. Expect array from callback');
                }
                foreach ($rawStringResult as $resultString) {
                    $result[] = $resultString;
                }
            }
        }
        return new StringCollection($result);
    }
}
