<?php

  namespace Test\Xparse\ElementFinder\Helper;

  /**
   * @author Ivan Shcherbak <alotofall@gmail.com>
   */
  class RegexHelperTest extends \PHPUnit_Framework_TestCase {

    public function testInvalidRegexForCallback() {
      $items = \Xparse\ElementFinder\Helper\RegexHelper::matchCallback('![a-z]!', function () {
        return [];
      }, ['1']);
      self::assertCount(0, $items);
    }

  }