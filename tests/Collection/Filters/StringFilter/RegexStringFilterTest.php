<?php

declare(strict_types=1);

namespace Test\Xparse\ElementFinder\Collection\Filters\StringFilter;

use PHPUnit\Framework\TestCase;
use Xparse\ElementFinder\Collection\Filters\StringFilter\RegexStringFilter;

final class RegexStringFilterTest extends TestCase
{
    public function testRegexSuccess(): void
    {
        self::assertTrue(
            (new RegexStringFilter('!^[a-z]+$!'))->valid('test')
        );
    }

    public function testRegexFailure(): void
    {
        self::assertFalse(
            (new RegexStringFilter('![0-9]+$!'))->valid('123user')
        );
    }
}
