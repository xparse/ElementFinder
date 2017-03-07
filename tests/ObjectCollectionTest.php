<?php

  namespace Xparse\Dom\ElementFinder;

  /**
   *
   * @package Xparse\Dom\ElementFinder
   */
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

  }