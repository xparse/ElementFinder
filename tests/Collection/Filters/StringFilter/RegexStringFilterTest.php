<?php

  namespace Tests\Xparse\ElementFinder\Collection\Filters\StringFilter;

  use PHPUnit\Framework\TestCase;
  use Xparse\ElementFinder\Collection\Filters\StringFilter\RegexStringFilter;

  class RegexStringFilterTest extends TestCase {


    public function testRegexSuccess() {
      self::assertTrue(
        (new RegexStringFilter('!^[a-z]+$!'))->valid('test')
      );
    }


    public function testRegexFailure() {
      self::assertFalse(
        (new RegexStringFilter('![0-9]+$!'))->valid('123user')
      );
    }

  }
