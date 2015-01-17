<?php

  namespace Test\Xparse\ElementFinder;

  /**
   *
   * @package Test\Xparse\ElementFinder
   */
  class LinkConverterTest extends \Test\Xparse\ElementFinder\Main {

    public function testSchemaUrl() {

      $data = '<html><a href="//funivan.com"></a></html>';
      $firstUrl = 'http://funivan.com/contacts/';

      $page = new \Xparse\ElementFinder\ElementFinder($data);

      $converter = (new \Xparse\ElementFinder\Helper\LinkConverter($page, $firstUrl));
      $converter->convert();

      $firstUrl = $page->attribute('//*/@href')->getFirst();
      $this->assertEquals('http://funivan.com', $firstUrl);
    }


    public function testEmptyUrls() {

      $data = '<html><a href=""></a></html>';
      $url = 'http://user:123@funivan.com:8585/contacts/?test#dd';

      $page = new \Xparse\ElementFinder\ElementFinder($data);

      $converter = (new \Xparse\ElementFinder\Helper\LinkConverter($page, $url));
      $converter->convert();

      $this->assertEquals($url, $page->attribute('//*/@href')->getFirst());

      $data = '<html><form action=""></form></html>';
      $url = 'http://funivan.com/contacts/?test#dd';

      $page = new \Xparse\ElementFinder\ElementFinder($data);

      $converter = (new \Xparse\ElementFinder\Helper\LinkConverter($page, $url));
      $converter->convert();

      $this->assertEquals($url, $page->attribute('//*/@action')->getFirst());

    }


    public function testCurrentLevelUrls() {

      $data = '<html><a href="./test.html"></a></html>';

      $urls = array(
        'http://user:123@funivan.com:8585/full/?test#dd'
        => 'http://user:123@funivan.com:8585/full/test.html',

        'http://funivan.com/contacts'
        => 'http://funivan.com/contacts/test.html'
      );

      foreach ($urls as $effectedUrl => $expect) {
        $page = new \Xparse\ElementFinder\ElementFinder($data);
        $converter = (new \Xparse\ElementFinder\Helper\LinkConverter($page, $effectedUrl));
        $converter->convert();

        $this->assertEquals($expect, $page->attribute('//*/@href')->getFirst());

      }

    }


    public function testCurrentTopUrl() {

      $data = '<html><a href="/dd.html"></a><a href="//dd.html"></a></html>';

      $urls = array(
        'http://user:123@funivan.com:8585/full/?test#dd'
        => 'http://user:123@funivan.com:8585/dd.html',

        'http://funivan.com/contacts'
        => 'http://funivan.com/dd.html'
      );

      foreach ($urls as $effectedUrl => $expect) {
        $page = new \Xparse\ElementFinder\ElementFinder($data);
        $converter = (new \Xparse\ElementFinder\Helper\LinkConverter($page, $effectedUrl));
        $converter->convert();

        $this->assertEquals($expect, $page->attribute('//*/@href')->getFirst());
        $this->assertNotEquals($expect, $page->attribute('//*/@href')->item(1));

      }

    }


    public function testUrlsWithQuery() {

      $data = '<html><a href="?df=123"></a></html>';

      $urls = array(
        'http://user:123@funivan.com:8585/full'
        => 'http://user:123@funivan.com:8585/full?df=123',

        'http://funivan.com/contacts/test/?id=123'
        => 'http://funivan.com/contacts/test/?df=123'
      );

      foreach ($urls as $effectedUrl => $expect) {
        $page = new \Xparse\ElementFinder\ElementFinder($data);
        $converter = (new \Xparse\ElementFinder\Helper\LinkConverter($page, $effectedUrl));
        $converter->convert();

        $this->assertEquals($expect, $page->attribute('//*/@href')->getFirst());

      }

    }


    public function testUrlsWithFragment() {

      $data = '<html><a href="#dfdt"></a></html>';

      $urls = array(
        'http://user:123@funivan.com:8585/full'
        => 'http://user:123@funivan.com:8585/full#dfdt',

        'http://funivan.com/contacts/test/?id=123'
        => 'http://funivan.com/contacts/test/?id=123#dfdt',

        'http://funivan.com/contacts/test/?id=123#test'
        => 'http://funivan.com/contacts/test/?id=123#dfdt'
      );

      foreach ($urls as $effectedUrl => $expect) {
        $page = new \Xparse\ElementFinder\ElementFinder($data);
        $converter = (new \Xparse\ElementFinder\Helper\LinkConverter($page, $effectedUrl));
        $converter->convert();

        $this->assertEquals($expect, $page->attribute('//*/@href')->getFirst());

      }

    }

    /**
     * @todo
     */
    public function __testConvertOtherUrls() {

      $data = '<html>
      <a href="test.com"></a>
      <a href="http://test.com"></a>
</html>';

      $urls = array(
        'http://user:123@funivan.com:8585/full'
        => 'http://user:123@funivan.com:8585/test.com',

        'http://funivan.com/contacts/test/?id=123'
        => 'http://funivan.com/contacts/test/?id=123#dfdt',

        'http://funivan.com/contacts/test/?id=123#test'
        => 'http://funivan.com/contacts/test/?id=123#dfdt'
      );

      foreach ($urls as $effectedUrl => $expect) {
        $page = new \Xparse\ElementFinder\ElementFinder($data);
        $converter = (new \Xparse\ElementFinder\Helper\LinkConverter($page, $effectedUrl));
        $converter->convert();

        $this->assertEquals($expect, $page->attribute('//*/@href')->getFirst());

      }

    }

  }