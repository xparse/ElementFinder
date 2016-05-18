<?php

  namespace Tests\Xparse\ElementFinder\Helper;

  use Xparse\ElementFinder\Helper\StringHelper;

  /**
   *
   */
  class StringHelperTest extends \PHPUnit_Framework_TestCase {

    public function testEncodeString() {
      $this->assertEquals('AA&lt;<', StringHelper::safeEncodeStr('AA&lt;&#60;'));
    }
  }
