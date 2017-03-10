<?php

  namespace Test\Xparse\ElementFinder\Collection;

  use Xparse\ElementFinder\Collection\ElementCollection;
  use Xparse\ElementFinder\ElementFinder\Element;

  class ElementCollectionTest extends \Test\Xparse\ElementFinder\Main {

    public function testAttributes() {
      $html = $this->getHtmlTestObject();

      $spanElements = $html->element('//span');
      $spanItems = $spanElements->getAttributes();

      self::assertCount(count($spanElements), $spanItems);
    }


    public function testItem() {
      $html = $this->getHtmlTestObject();

      $spanElements = $html->element('//span');
      self::assertCount(4, $spanElements);
      self::assertNull($spanElements->item(20));

      self::assertInstanceOf(Element::class, $spanElements->item(0));

    }


    public function testWalk() {
      $collection = new ElementCollection(
        [
          new Element('a', 'link'),
          new Element('b', 'bold'),
        ]
      );

      $items = [];
      $collection->walk(function (Element $element) use (&$items) {
        $items[] = $element->tagName . '.' . $element->nodeValue;
      });
      self::assertSame(['a.link', 'b.bold'], $items);
    }

  }