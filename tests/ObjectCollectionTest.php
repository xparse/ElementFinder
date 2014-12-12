<?php

  namespace Xparse\Dom\ElementFinder;

  class ObjectCollectionTest extends \Xparse\ElementFinder\Test\Main {

    public function testObjectWithOuterHtml() {
      $html = $this->getHtmlTestObject();

      $spanItems = $html->object('//span', true);

      $this->assertCount(4, $spanItems);

      $firstItem = $spanItems->item(0);

      $this->assertContains('<span class="span-1">', (string)$firstItem);

    }


    public function testInvalidObjectIndex() {
      $html = $this->getHtmlTestObject();
      $spanItems = $html->object('//span');
      $this->assertCount(4, $spanItems);

      $span = $spanItems->item(5);
      $this->assertNull($span);

      $span = $spanItems->item(0);
      $this->assertNotNull($span);
    }


    public function testReplace() {
      $html = $this->getHtmlTestObject();
      $spanItems = $html->object('//span[@class]', true);
      $this->assertCount(3, $spanItems);

      $spanItems->replace('!span-(\d+)!', 'class-span--$1');

      foreach ($spanItems as $index => $item) {
        $class = $item->attribute('//@class')->getFirst();
        $expectClass = 'class-span--' . ($index + 1);
        $this->assertEquals($expectClass, $class);
      }

      $spanItems->replace('!class=".*"!U');

      foreach ($spanItems as $index => $item) {
        $classAttributes = $item->attribute('//@class');
        $this->assertCount(0, $classAttributes);
      }

    }

  } 