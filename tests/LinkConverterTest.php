<?

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

      (new \Xparse\ElementFinder\Helper\LinkConverter($page, $firstUrl))->convert();

      $firstUrl = $page->attribute('//*/@href')->getFirst();
      $this->assertEquals('http://funivan.com', $firstUrl);
    }

  }