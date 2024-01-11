<?php

declare(strict_types=1);

namespace Test\Xparse\ElementFinder\Collection;

use PHPUnit\Framework\TestCase;
use Xparse\ElementFinder\Collection\ElementCollection;
use Xparse\ElementFinder\ElementFinder\Element;

/**
 * @author Ivan Shcherbak <alotofall@gmail.com>
 */
class ElementCollectionTest extends TestCase
{
    public function testIterate(): void
    {
        $collection = new ElementCollection(
            [
                new Element('a', 'class0'),
                new Element('b', 'class1'),
            ]
        );

        $collectedItems = 0;
        foreach ($collection as $index => $item) {
            $collectedItems++;
            self::assertSame('class' . $index, $item->nodeValue);
        }

        self::assertSame(2, $collectedItems);
    }

    public function testMerge(): void
    {
        $collection = (new ElementCollection([new Element('a', 'link')]))->merge(new ElementCollection([new Element('b', 'bold')]));
        self::assertSame(['a.link', 'b.bold'], [
            $collection->first()->tagName . '.' . $collection->first()->nodeValue,
            $collection->last()->tagName . '.' . $collection->last()->nodeValue,
        ]);
    }

    public function testGetLast(): void
    {
        $collection = new ElementCollection([new Element('a', 'link'), new Element('b', 'bold')]);
        self::assertSame('b', $collection->last()->tagName);
    }

    public function testGetLastOnEmptyCollection(): void
    {
        $collection = new ElementCollection();
        self::assertNull($collection->last());
    }

    public function testGetFirst(): void
    {
        $collection = new ElementCollection([new Element('a', 'link'), new Element('b', 'bold')]);
        self::assertSame('a', $collection->first()->tagName);
    }

    public function testGetFirstOnEmptyCollection(): void
    {
        $collection = new ElementCollection();
        self::assertNull($collection->first());
    }

    public function testAdd(): void
    {
        $collection = new ElementCollection();
        $newCollection = $collection->add(new Element('a', 'link'));

        self::assertCount(0, $collection);
        self::assertCount(1, $newCollection);
    }

    public function testGet(): void
    {
        $collection = new ElementCollection([new Element('span', 'link')]);
        self::assertSame('span', $collection->get(0)->tagName);
        self::assertNull($collection->get(1));
    }
}
