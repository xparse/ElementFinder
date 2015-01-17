<?php

  namespace Xparse\Dom\ElementFinder;

  /**
   *
   * @package Xparse\Dom\ElementFinder
   */
  class StringCollectionTest extends \Test\Xparse\ElementFinder\Main {

    public function testInvalidObjectIndex() {
      $html = $this->getHtmlTestObject();
      $spanItems = $html->html('//span');
      $this->assertCount(4, $spanItems);

      $span = $spanItems->item(5);
      $this->assertEquals('', $span);

      $span = $spanItems->item(0);
      $this->assertNotEmpty($span);
    }

    public function testReplace() {
      $html = $this->getHtmlTestObject();
      $spanItems = $html->html('//span[@class]');
      $this->assertCount(3, $spanItems);

      $spanItems->replace('!<[\/]*[a-z]+>!');

      foreach ($spanItems as $index => $item) {
        $expectClass = ($index + 1) . ' r';
        $this->assertEquals($expectClass, $item);
      }

      $spanItems->replace('![a-z<\/>]!U', '0');

      foreach ($spanItems as $index => $item) {
        $expectClass = ($index + 1) . ' 0';
        $this->assertEquals($expectClass, $item);
      }

    }

    public function testMatch() {
      $html = $this->getHtmlTestObject();
      $spanItems = $html->html('//span[@class]');
      $this->assertCount(3, $spanItems);

      $tags = $spanItems->match('!(<[a-z]+>.)!');

      $this->assertCount(6, $tags);
      foreach ($tags as $index => $item) {
        $result = preg_match('!^<[b|i]!', $item);
        $this->assertTrue(!empty($result));
      }

      $tags = $spanItems->match('!<([a-z]+)>.!');

      $this->assertCount(6, $tags);
      foreach ($tags as $index => $item) {
        $result = preg_match('!^[b|i]$!', $item);
        $this->assertTrue(!empty($result));
      }

    }

    public function testSplit() {
      $html = $this->getHtmlDataObject();
      $telsDiv = $html->html('//*[@id="tels"]');
      $this->assertCount(1, $telsDiv);

      $tels = $telsDiv->replace('!\s*!')->split('!<br[/]>!');

      $this->assertCount(2, $tels);

      foreach ($tels as $index => $item) {
        $result = preg_match('!^([\d-]+)$!', $item);
        $this->assertTrue(!empty($result));
      }

    }

  } 