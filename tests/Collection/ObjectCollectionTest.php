<?php

  namespace Test\Xparse\ElementFinder\Collection;

  use Xparse\ElementFinder\ElementFinder;

  class ObjectCollectionTest extends \Test\Xparse\ElementFinder\Main {

    public function testObjectWithOuterHtml() {
      $html = $this->getHtmlTestObject();

      $spanItems = $html->object('//span', true);

      self::assertCount(4, $spanItems);

      $firstItem = $spanItems->item(0);

      self::assertContains('<span class="span-1">', (string) $firstItem);

    }


    public function testInvalidObjectIndex() {
      $html = $this->getHtmlTestObject();
      $spanItems = $html->object('//span');
      self::assertCount(4, $spanItems);

      $span = $spanItems->item(5);
      self::assertNull($span);

      $span = $spanItems->item(0);
      self::assertNotNull($span);
    }


    public function testReplace() {
      $html = $this->getHtmlTestObject();
      $spanItems = $html->object('//span[@class]', true);
      self::assertCount(3, $spanItems);

      $spanItems->replace('!span-(\d+)!', 'class-span--$1');

      foreach ($spanItems as $index => $item) {
        $class = $item->value('//@class')->getFirst();
        $expectClass = 'class-span--' . ($index + 1);
        self::assertEquals($expectClass, $class);
      }

      $spanItems->replace('!class=".*"!U');

      foreach ($spanItems as $index => $item) {
        $classAttributes = $item->value('//@class');
        self::assertCount(0, $classAttributes);
      }

    }


    public function testWalk() {
      $collection = new \Xparse\ElementFinder\Collection\ObjectCollection(
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
      $collection = new \Xparse\ElementFinder\Collection\ObjectCollection(
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


  }