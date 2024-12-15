<?php

declare(strict_types=1);

namespace Test\Xparse\ElementFinder\Collection\Modify\StringModify;

use PHPUnit\Framework\TestCase;
use Xparse\ElementFinder\Collection\Modify\StringModify\RegexReplace;
use Xparse\ElementFinder\Collection\StringCollection;

final class RegexReplaceTest extends TestCase
{
    public function testReplace(): void
    {
        $collection = new StringCollection(['test-1', 'test--123', '--3']);
        $collection = $collection->map(new RegexReplace('!([a-z]+)-+!', '$1::'));
        self::assertSame(
            [
                'test::1',
                'test::123',
                '--3',
            ],
            $collection->all()
        );
    }
}
