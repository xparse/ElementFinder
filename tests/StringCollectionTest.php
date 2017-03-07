<?php

  namespace Xparse\Dom\ElementFinder;

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
        $result = preg_match('!^<[b|i]!', $item);
        self::assertTrue(!empty($result));
      }

      $tags = $spanItems->match('!<([a-z]+)>.!');

      self::assertCount(6, $tags);
      foreach ($tags as $index => $item) {
        $result = preg_match('!^[b|i]$!', $item);
        self::assertTrue(!empty($result));
      }

    }


    public function testSplit() {
      $html = $this->getHtmlDataObject();
      $telsDiv = $html->content('//*[@id="tels"]');
      self::assertCount(1, $telsDiv);

      $tels = $telsDiv->replace('!\s*!')->split('!<br[/]>!');

      self::assertCount(2, $tels);

      foreach ($tels as $index => $item) {
        $result = preg_match('!^([\d-]+)$!', $item);
        self::assertTrue(!empty($result));
      }

    }

  }