<?php

  namespace Test\Xparse\ElementFinder;

  /**
   *
   * @package Test\Xparse\ElementFinder
   */
  class ElementCollectionTest extends \Test\Xparse\ElementFinder\Main {

    public function testAttributes() {
      $html = $this->getHtmlTestObject();

      $spanElements = $html->element("//span");
      $spanItems = $spanElements->getAttributes();

      self::assertCount(count($spanElements), $spanItems);
    }

    public function testItem() {
      $html = $this->getHtmlTestObject();

      $spanElements = $html->element("//span");
      self::assertCount(4, $spanElements);
      self::assertNull($spanElements->item(20));

      self::assertInstanceOf('\Xparse\ElementFinder\ElementFinder\Element', $spanElements->item(0));

    }
  }