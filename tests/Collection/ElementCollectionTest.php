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


    public function testIterate()
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


    public function testMerge()
    {
        $collection = (new ElementCollection([new Element('a', 'link')]))->merge(new ElementCollection([new Element('b', 'bold')]));
        self::assertSame(['a.link', 'b.bold'], [
            $collection->getFirst()->tagName . '.' . $collection->getFirst()->nodeValue,
            $collection->getLast()->tagName . '.' . $collection->getLast()->nodeValue,
        ]);
    }


    public function testGetLast()
    {
        $collection = new ElementCollection([new Element('a', 'link'), new Element('b', 'bold')]);
        self::assertSame('b', $collection->getLast()->tagName);
    }


    public function testGetLastOnEmptyCollection()
    {
        $collection = new ElementCollection();
        self::assertNull($collection->getLast());
    }


    public function testGetFirst()
    {
        $collection = new ElementCollection([new Element('a', 'link'), new Element('b', 'bold')]);
        self::assertSame('a', $collection->getFirst()->tagName);
    }


    public function testGetFirstOnEmptyCollection()
    {
        $collection = new ElementCollection();
        self::assertNull($collection->getFirst());
    }


    public function testAdd()
    {
        $collection = new ElementCollection();
        $newCollection = $collection->add(new Element('a', 'link'));

        self::assertCount(0, $collection);
        self::assertCount(1, $newCollection);
    }


    public function testGet()
    {
        $collection = new ElementCollection([new Element('span', 'link')]);
        self::assertSame('span', $collection->get(0)->tagName);
        self::assertNull($collection->get(1));
    }
}
