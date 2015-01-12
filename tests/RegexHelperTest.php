<?php

  namespace Test\Xparse\ElementFinder;

  /**
   * @author Ivan Shcherbak <dev@funivan.com> 1/11/15
   */
  class RegexHelperTest extends \Test\Xparse\ElementFinder\Main {

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidIndex() {
      \Xparse\ElementFinder\Helper\RegexHelper::match('![a-z]!', null, array());
    }


    
    public function testInvalidRegexForCallback() {
      $items = \Xparse\ElementFinder\Helper\RegexHelper::matchCallback('![a-z]!', function () {
        return array();
      }, array('1'));
      $this->assertCount(0, $items);
    }

  }