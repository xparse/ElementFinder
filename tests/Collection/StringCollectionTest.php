<?php

  declare(strict_types=1);

  namespace Test\Xparse\ElementFinder\Collection;

  use PHPUnit\Framework\TestCase;
  use Xparse\ElementFinder\Collection\Filters\StringFilter\StringFilterInterface;
  use Xparse\ElementFinder\Collection\StringCollection;

  /**
   * @author Ivan Shcherbak <alotofall@gmail.com>
   */
  class StringCollectionTest extends TestCase {

    public function testInvalidObjectIndex() {
      $collection = new StringCollection(['a-1', 'b.2', 'c,3']);
      self::assertEquals('a-1', $collection->get(0));
      self::assertEquals(null, $collection->get(3));
    }


    public function testReplace() {
      $mainCollection = new StringCollection(['a-1', 'b.2', 'c,3']);
      $collection = $mainCollection->replace('![-,.]!', '::');
      self::assertSame(['a::1', 'b::2', 'c::3'], $collection->getItems());
      self::assertSame(['a-1', 'b.2', 'c,3'], $mainCollection->getItems());
    }


    public function testMatch() {
      $collection = new StringCollection(['a-1', 'b.2', 'c,3']);
      $collection = $collection->match('/[a-z][-,](\d)/');
      self::assertSame(['1', '3'], $collection->getItems());
    }


    public function testSplit() {
      $collection = new StringCollection(['a-1', 'b.2']);
      $collection = $collection->split('/[.-]/');
      self::assertSame(['a', '1', 'b', '2'], $collection->getItems());
    }


    public function testWalk() {
      $collection = new \Xparse\ElementFinder\Collection\StringCollection(['1', '2', '3']);
      $data = '';
      $collection->walk(function (string $item) use (&$data) {
        $data = $data . $item;
      });
      self::assertSame('123', $data);
    }


    public function testUnique() {
      $collection = new StringCollection(['1', '2', '2']);
      self::assertCount(3, $collection);

      $collection = $collection->unique();
      self::assertCount(2, $collection);
    }


    public function testIterate() {
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


    public function testMergeWithItems() {
      $collection = (new StringCollection(['a', 'b']))->merge(new StringCollection(['a', 'c']));
      self::assertSame(['a', 'b', 'a', 'c'], $collection->getItems());
    }


    public function testMergeWithoutItems() {
      $collection = (new StringCollection())->merge(new StringCollection());
      self::assertSame([], $collection->getItems());
    }


    public function testMergeWithPartialItems() {
      $collection = (new StringCollection([1 => 'a']))->merge(new StringCollection([1 => 'b', 'c']));
      self::assertSame(['a', 'b', 'c'], $collection->getItems());
    }


    public function testAdd() {
      $sourceCollection = new StringCollection([1 => 'a']);
      $newCollection = $sourceCollection->add('b');
      self::assertSame(['a'], $sourceCollection->getItems());
      self::assertSame(['a', 'b'], $newCollection->getItems());
    }


    public function testGet() {
      $collection = new StringCollection([1 => 'a']);
      self::assertSame('a', $collection->get(0));
      self::assertSame(null, $collection->get(1));
    }


    public function testGetLast() {
      $collection = new StringCollection([1 => 'word']);
      self::assertSame('word', $collection->getLast());
    }


    public function testFilter() {
      $collection = new StringCollection(['foo', 'bar', 'baz']);
      $collection = $collection->filter(new class implements StringFilterInterface {
        public function valid(string $input): bool {
          return strpos($input, 'a') !== false;
        }
      });

      self::assertSame(
        [
          'bar', 'baz',
        ],
        $collection->getItems()
      );
    }

  }