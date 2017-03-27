<?php

  namespace Tests\Xparse\ElementFinder\Helper;

  use Xparse\ElementFinder\Helper\StringHelper;

  /**
   * @author Ivan Shcherbak <alotofall@gmail.com>
   */
  class StringHelperTest extends \PHPUnit_Framework_TestCase {


    public function testEncode() {

      $data = [
        'AA&lt;<' => 'AA&lt;&#60;',
        '<' => '&#60;',
        '> &#s' => '&#62; &#s',
        '&  ' => '&#38;  ',
        '¢' => '&#162;',
        '£' => '&#163;',
        '¥' => '&#165;',
        '€' => '&#8364;',
        '©' => '&#169;',
        'Data ®' => 'Data &#174;',
      ];

      foreach ($data as $expect => $string) {
        $output = StringHelper::safeEncodeStr($string);
        self::assertEquals($expect, $output);
      }

    }
  }
