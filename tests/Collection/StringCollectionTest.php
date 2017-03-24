<?php

  namespace Test\Xparse\ElementFinder\Collection;

  use Xparse\ElementFinder\Collection\StringCollection;


  class StringCollectionTest extends \Test\Xparse\ElementFinder\Main {

    public function testInvalidObjectIndex() {
      $html = $this->getHtmlTestObject();
      $spanItems = $html->content('//span');
      self::assertCount(4, $spanItems);

      $span = $spanItems->item(5);
      self::assertEquals('', $span);

      $span = $spanItems->item(0);
      self::assertNotEmpty($span);
    }


    public function testReplace() {
      $html = $this->getHtmlTestObject();
      $spanItems = $html->content('//span[@class]');
      self::assertCount(3, $spanItems);

      $spanItems->replace('!<[/]*[a-z]+>!');

      foreach ($spanItems as $index => $item) {
        $expectClass = ($index + 1) . ' r';
        self::assertEquals($expectClass, $item);
      }

      $spanItems->replace('![a-z</>]!U', '0');

      foreach ($spanItems as $index => $item) {
        $expectClass = ($index + 1) . ' 0';
        self::assertEquals($expectClass, $item);
      }

    }


    public function testMatch() {
      $html = $this->getHtmlTestObject();
      $spanItems = $html->content('//span[@class]');
      self::assertCount(3, $spanItems);

      $tags = $spanItems->match('!(<[a-z]+>.)!');

      self::assertCount(6, $tags);
      foreach ($tags as $index => $item) {
        self::assertSame(preg_match('!^<[b|i]!', $item), 1);
      }

      $tags = $spanItems->match('!<([a-z]+)>.!');

      self::assertCount(6, $tags);
      foreach ($tags as $index => $item) {
        self::assertSame(preg_match('!^[b|i]$!', $item), 1);
      }

    }


    public function testSplit() {
      $html = $this->getHtmlDataObject();
      $telsDiv = $html->content('//*[@id="tels"]');
      self::assertCount(1, $telsDiv);

      $tels = $telsDiv->replace('!\s*!')->split('!<br[/]>!');

      self::assertCount(2, $tels);

      foreach ($tels as $index => $item) {
        self::assertSame(preg_match('!^([\d-]+)$!', $item), 1);
      }

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


  }