<?php

declare(strict_types=1);

namespace Test\Xparse\ElementFinder\Collection;

use PHPUnit\Framework\TestCase;
use Xparse\ElementFinder\Collection\ObjectCollection;
use Xparse\ElementFinder\ElementFinder;

/**
 * @author Ivan Shcherbak <alotofall@gmail.com>
 */
class ObjectCollectionTest extends TestCase
{
    public function testInvalidObjectIndex()
    {
        $collection = new ObjectCollection([new ElementFinder('<a>0</a>'), new ElementFinder('<a>1</a>')]);
        self::assertNotNull($collection->get(0));
        self::assertEquals(null, $collection->get(2));
    }


    public function testIterate()
    {
        $collection = new ObjectCollection(
            [
                new ElementFinder('<a>0</a>'),
                new ElementFinder('<a>1</a>'),
            ]
        );

        $collectedItems = 0;
        foreach ($collection as $index => $item) {
            $collectedItems++;
            $data = $item->content('.')->match('!<a>(.*)</a>!')->first();
            self::assertSame((string)$index, $data);
        }

        self::assertSame(2, $collectedItems);
    }


    public function testMerge()
    {
        $sourceCollection = new ObjectCollection([new ElementFinder('<a>0</a>'), new ElementFinder('<a>1</a>')]);
        $newCollection = new ObjectCollection([new ElementFinder('<a>0</a>')]);

        $mergedCollection = $sourceCollection->merge($newCollection);
        $result = [];
        foreach ($mergedCollection as $element) {
            $result[] = $element->value('//a')->first();
        }
        self::assertSame(['0', '1', '0'], $result);
    }


    public function testAdd()
    {
        $sourceCollection = new ObjectCollection([new ElementFinder('<a>0</a>'), new ElementFinder('<a>1</a>')]);
        $newCollection = $sourceCollection->add(new ElementFinder('<a>2</a>'));
        self::assertCount(2, $sourceCollection);
        self::assertCount(3, $newCollection);
        self::assertSame('2', $newCollection->last()->content('//a')->first());
    }


    public function testGet()
    {
        $collection = new ObjectCollection([new ElementFinder('<b>0</b>'), new ElementFinder('<a>data1</a>')]);
        self::assertNotNull('data1', $collection->get(0)->content('//b')->first());
        self::assertNotNull('data1', $collection->get(1)->content('//a')->first());
        self::assertNull($collection->get(2));
    }


    public function testInvalidDataType()
    {
        $this->expectException(\InvalidArgumentException::class);
        (new ObjectCollection([null]))->all();
    }
}
