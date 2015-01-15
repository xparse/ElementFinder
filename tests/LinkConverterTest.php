<?php

  namespace Test\Xparse\ElementFinder;

  /**
   *
   * @package Test\Xparse\ElementFinder
   */
  class LinkConverterTest extends \Test\Xparse\ElementFinder\Main {

    public function testSchemaUrl() {


      $firstUrl = 'http://funivan.com/contacts/';
      $page = new \Xparse\ElementFinder\ElementFinder('<html><a href="//funivan.com"></a></html>');

      $converter = (new \Xparse\ElementFinder\Helper\LinkConverter($page, $firstUrl));
      $converter->convert();

      $firstUrl = $page->attribute('//*/@href')->getFirst();
      $this->assertEquals('http://funivan.com', $firstUrl);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testPassInvalidUrl() {
      $page = new \Xparse\ElementFinder\ElementFinder('<html></html>');
      new \Xparse\ElementFinder\Helper\LinkConverter($page, new \stdClass());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testPassUrlWithoutSchema() {
      $page = new \Xparse\ElementFinder\ElementFinder('<html></html>');
      new \Xparse\ElementFinder\Helper\LinkConverter($page, '//funivan.com');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testPassUrlWithoutHost() {
      $page = new \Xparse\ElementFinder\ElementFinder('<html></html>');
      new \Xparse\ElementFinder\Helper\LinkConverter($page, 'http:///d');
    }


    public function testSchemaDetect() {
      $page = new \Xparse\ElementFinder\ElementFinder('<html></html>');
      $converter = new \Xparse\ElementFinder\Helper\LinkConverter($page, 'http://test/d');
      $this->assertEquals('http://', $converter->getUrlSchema());
    }

    public function testPathDetect() {
      $page = new \Xparse\ElementFinder\ElementFinder('<html></html>');
      $converter = new \Xparse\ElementFinder\Helper\LinkConverter($page, 'http://test/d');
      $this->assertEquals('/d', $converter->getUrlPath());

      $converter = new \Xparse\ElementFinder\Helper\LinkConverter($page, 'http://test/');
      $this->assertEquals('/', $converter->getUrlPath());

    }

    public function testFragmentDetect() {
      $page = new \Xparse\ElementFinder\ElementFinder('<html></html>');
      $converter = new \Xparse\ElementFinder\Helper\LinkConverter($page, 'http://test/d');
      $this->assertEmpty($converter->getUrlFragment());

      $converter = new \Xparse\ElementFinder\Helper\LinkConverter($page, 'http://t#t/d#df');
      $this->assertEquals('#df', $converter->getUrlFragment());

    }


    public function testUrlHostTest() {
      $page = new \Xparse\ElementFinder\ElementFinder('<html></html>');

      $data = array(
        'user@funivan.com' => 'http://user@funivan.com/d',
        'user:123@funivan.com' => 'http://user:123@funivan.com/d',
        ':123@funivan.com' => 'http://:123@funivan.com/d',
        'test:123@funivan.com:45' => 'http://test:123@funivan.com:45/d',
      );


      foreach ($data as $contains => $url) {
        $converter = new \Xparse\ElementFinder\Helper\LinkConverter($page, $url);
        $this->assertContains($contains, $converter->getUrlHost());
      }

    }

  }