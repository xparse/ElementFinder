<?php

  namespace Test\Xparse\ElementFinder\Helper;

  /**
   * @author Ivan Shcherbak <dev@funivan.com> 1/11/15
   */
  class RegexHelperTest extends \Test\Xparse\ElementFinder\Main {

    public function testInvalidRegexForCallback() {
      $items = \Xparse\ElementFinder\Helper\RegexHelper::matchCallback('![a-z]!', function () {
        return [];
      }, ['1']);
      self::assertCount(0, $items);
    }

  }