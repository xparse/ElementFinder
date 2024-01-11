<?php

declare(strict_types=1);

namespace Test\Xparse\ElementFinder\Collection;

use PHPUnit\Framework\TestCase;
use Test\Xparse\ElementFinder\Collection\Dummy\JoinedBy;
use Test\Xparse\ElementFinder\Collection\Dummy\WithLetterFilter;
use Xparse\ElementFinder\Collection\StringCollection;

/**
 * @author Ivan Shcherbak <alotofall@gmail.com>
 */
class StringCollectionTest extends TestCase
{
    public function testInvalidObjectIndex(): void
    {
        $collection = new StringCollection(['a-1', 'b.2', 'c,3']);
        self::assertEquals('a-1', $collection->get(0));
        self::assertEquals(null, $collection->get(3));
    }

    public function testReplace(): void
    {
        $mainCollection = new StringCollection(['a-1', 'b.2', 'c,3']);
        $collection = $mainCollection->replace('![-,.]!', '::');
        self::assertSame(['a::1', 'b::2', 'c::3'], $collection->all());
        self::assertSame(['a-1', 'b.2', 'c,3'], $mainCollection->all());
    }

    public function testMatch(): void
    {
        $collection = new StringCollection(['a-1', 'b.2', 'c,3']);
        $collection = $collection->match('/[a-z][-,](\d)/');
        self::assertSame(['1', '3'], $collection->all());
    }

    public function testSplit(): void
    {
        $collection = new StringCollection(['a-1', 'b.2']);
        $collection = $collection->split('/[.-]/');
        self::assertSame(['a', '1', 'b', '2'], $collection->all());
    }

    public function testUnique(): void
    {
        $collection = new StringCollection(['1', '2', '2']);
        self::assertCount(3, $collection);

        $collection = $collection->unique();
        self::assertCount(2, $collection);
    }

    public function testIterate(): void
    {
        $collection = new StringCollection(
            [
                'element-0',
                'element-1',
                'element-2',
            ]
        );

        $collectedItems = 0;
        foreach ($collection as $index => $item) {
            $collectedItems++;
            self::assertSame('element-' . $index, $item);
        }

        self::assertSame(3, $collectedItems);
    }

    public function testMergeWithItems(): void
    {
        $collection = (new StringCollection(['a', 'b']))->merge(new StringCollection(['a', 'c']));
        self::assertSame(['a', 'b', 'a', 'c'], $collection->all());
    }

    public function testMergeWithoutItems(): void
    {
        $collection = (new StringCollection())->merge(new StringCollection());
        self::assertSame([], $collection->all());
    }

    public function testMergeWithPartialItems(): void
    {
        $collection = (new StringCollection([
            1 => 'a',
        ]))->merge(new StringCollection([
            1 => 'b',
            'c',
        ]));
        self::assertSame(['a', 'b', 'c'], $collection->all());
    }

    public function testAdd(): void
    {
        $sourceCollection = new StringCollection([
            1 => 'a',
        ]);
        $newCollection = $sourceCollection->add('b');
        self::assertSame(['a'], $sourceCollection->all());
        self::assertSame(['a', 'b'], $newCollection->all());
    }

    public function testGet(): void
    {
        $collection = new StringCollection([
            1 => 'a',
        ]);
        self::assertSame('a', $collection->get(0));
        self::assertNull($collection->get(1));
    }

    public function testGetLast(): void
    {
        $collection = new StringCollection([
            1 => 'word',
        ]);
        self::assertSame('word', $collection->last());
    }

    public function testFilter(): void
    {
        $collection = new StringCollection(['foo', 'bar', 'baz']);
        $collection = $collection->filter(new WithLetterFilter('a'));

        self::assertSame(
            [
                'bar', 'baz',
            ],
            $collection->all()
        );
    }

    public function testMap(): void
    {
        $collection = new StringCollection(['123', 'abc', 'test']);
        $collection = $collection->map(new JoinedBy('..'));
        self::assertSame(
            [
                '123..123',
                'abc..abc',
                'test..test',
            ],
            $collection->all()
        );
    }

    /**
     * @dataProvider lastDataProvider
     */
    public function testLast(array $items, mixed $expected): void
    {
        $collection = new StringCollection($items);
        $this->assertEquals($expected, $collection->last());
    }

    public static function lastDataProvider(): array
    {
        return [
            [['a', 'b', 'c'], 'c'],
            [['a', 'b', 'c', 'd'], 'd'],
            [['a', 'b', 'c', 'd', 'e'], 'e'],
            [['a', 'b', 'c', 'd', 'e', 'f'], 'f'],
            [[], null],
        ];
    }
}
