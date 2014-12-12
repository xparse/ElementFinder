<?php

  namespace Xparse\ElementFinder\Test;

  class ElementCollectionTest extends \Xparse\ElementFinder\Test\Main {

    public function testAttributes() {
      $html = $this->getHtmlTestObject();

      $spanElements = $html->elements("//span");
      $spanItems = $spanElements->getAttributes();

      $this->assertCount(count($spanElements), $spanItems);
    }

    public function testItem() {
      $html = $this->getHtmlTestObject();

      $spanElements = $html->elements("//span");
      $this->assertCount(4, $spanElements);
      $this->assertNull($spanElements->item(20));

      $this->assertInstanceOf('\Xparse\ElementFinder\ElementFinder\Element', $spanElements->item(0));

    }
  } 