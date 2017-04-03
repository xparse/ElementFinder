<?php

  namespace Test\Xparse\ElementFinder\Collection;

  use Xparse\ElementFinder\Collection\ObjectCollection;
  use Xparse\ElementFinder\ElementFinder;

  /**
   * @author Ivan Shcherbak <alotofall@gmail.com>
   */
  class ObjectCollectionTest extends \PHPUnit_Framework_TestCase {


    public function testInvalidObjectIndex() {
      $collection = new ObjectCollection([new ElementFinder('<a>0</a>'), new ElementFinder('<a>1</a>')]);
      self::assertNotNull($collection->item(0));
      self::assertEquals(null, $collection->item(2));
    }


    public function testReplace() {
      $collection = new ObjectCollection([new ElementFinder('<a>4</a>'), new ElementFinder('<a>10</a>')]);
      $collection = $collection->replace('!(\d+)!', 'test::$1');

      $items = [];
      foreach ($collection as $item) {
        $items[] = $item->content('//a')->getFirst();
      }

      self::assertSame(['test::4', 'test::10'], $items);

    }


    public function testWalk() {
      $collection = new ObjectCollection(
        [
          new ElementFinder('<a>1</a>'),
          new ElementFinder('<a>2</a>'),
        ]
      );

      $linksTest = [];
      $collection->walk(function (ElementFinder $elementFinder) use (&$linksTest) {
        $linksTest[] = $elementFinder->content('//a')->getFirst();
      });
      self::assertSame(['1', '2'], $linksTest);
    }


    public function testIterate() {
      $collection = new ObjectCollection(
        [
          new ElementFinder('<a>0</a>'),
          new ElementFinder('<a>1</a>'),
        ]
      );

      $collectedItems = 0;
      foreach ($collection as $index => $item) {
        $collectedItems++;
        $data = $item->match('!<a>(.*)</a>!')->getFirst();
        self::assertSame((string) $index, $data);
      }

      self::assertSame(2, $collectedItems);
    }


    public function testMerge() {
      $sourceCollection = new ObjectCollection([new ElementFinder('<a>0</a>'), new ElementFinder('<a>1</a>')]);
      $newCollection = new ObjectCollection([new ElementFinder('<a>0</a>')]);

      $mergedCollection = $sourceCollection->merge($newCollection);

      $aTexts = [];
      $mergedCollection->walk(function (ElementFinder $element) use (&$aTexts) {
        $aTexts[] = (string) $element->value('//a')->getFirst();
      });
      self::assertSame(['0', '1', '0'], $aTexts);
    }


    public function testAdd() {
      $sourceCollection = new ObjectCollection([new ElementFinder('<a>0</a>'), new ElementFinder('<a>1</a>')]);
      $newCollection = $sourceCollection->add(new ElementFinder('<a>2</a>'));
      self::assertCount(2, $sourceCollection);
      self::assertCount(3, $newCollection);
      self::assertCount('2', $newCollection->getLast()->content('//a')->getFirst());
    }


    public function testGet() {
      $collection = new ObjectCollection([new ElementFinder('<b>0</b>'), new ElementFinder('<a>data1</a>')]);
      self::assertNotNull('data1', $collection->get(0)->content('//b')->getFirst());
      self::assertNotNull('data1', $collection->get(1)->content('//a')->getFirst());
      self::assertNull($collection->get(2));
    }


    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidDataType() {
      new ObjectCollection([null]);
    }

  }