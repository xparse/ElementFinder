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

      $this->assertCount(count($spanElements), $spanItems);
    }

    public function testItem() {
      $html = $this->getHtmlTestObject();

      $spanElements = $html->element("//span");
      $this->assertCount(4, $spanElements);
      $this->assertNull($spanElements->item(20));

      $this->assertInstanceOf('\Xparse\ElementFinder\ElementFinder\Element', $spanElements->item(0));

    }
  } 