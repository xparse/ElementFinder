<?php

  namespace Test\Xparse\ElementFinder;

  use Test\Xparse\ElementFinder\Dummy\ItemsByClassExpressionTranslator;
  use Xparse\ElementFinder\ElementFinder;

  /**
   * @author Ivan Shcherbak <alotofall@gmail.com>
   */
  class ElementFinderTest extends \PHPUnit_Framework_TestCase {

    public function testLoad() {
      $html = $this->getHtmlTestObject();
      self::assertContains('<title>test doc</title>', (string) $html);
    }


    /**
     * @expectedException \Exception
     */
    public function testInvalidType() {
      new ElementFinder('', 'df');
    }


    /**
     * @expectedException \InvalidArgumentException
     */
    public function testLoadEmptyDoc() {
      new ElementFinder('');
    }


    /**
     *
     */
    public function testLoadDocumentWithZero() {
      $elementFinder = new ElementFinder('   0 ');
      self::assertContains('0', (string) $elementFinder);
    }


    public function testNodeList() {

      $html = $this->getHtmlTestObject();
      $spanNodes = $html->node('//span');

      self::assertInstanceOf(\DOMNodeList::class, $spanNodes);

      self::assertEquals(4, $spanNodes->length);
    }


    public function testAttributes() {
      $html = $this->getHtmlTestObject();

      $links = $html->value('//a/@href');

      self::assertCount(1, $links);

      foreach ($html->content('//a') as $htmlString) {
        self::assertTrue(is_string($htmlString));
      }

      $firstLink = $html->content('//a', true)->item(0);

      self::assertContains('<a href="http://funivan.com/" title="my blog">link</a>', (string) $firstLink);
    }


    public function testObjects() {
      $html = $this->getHtmlTestObject();

      $spanItems = $html->object('//span');

      self::assertCount(4, $spanItems);

      /** @var ElementFinder $span */
      foreach ($spanItems->extractItems(0, 3) as $index => $span) {
        $itemHtml = $span->content('//i')->item(0);

        self::assertEquals('r', trim($itemHtml));

      }

      $html->remove('//span[2]');

      $spanItems = $html->content('//span');
      self::assertCount(3, $spanItems);

      $html->remove('//span[@class]');

      $spanItems = $html->content('//span');
      self::assertCount(1, $spanItems);

    }


    public function testObjectWithOuterHtml() {
      $html = $this->getHtmlTestObject();

      $spanItems = $html->object('//span', true);

      self::assertCount(4, $spanItems);

      $firstItem = $spanItems->item(0);

      self::assertContains('<span class="span-1">', (string) $firstItem);

    }


    public function testDeleteNode() {
      $html = $this->getHtmlTestObject();

      $title = $html->value('//title')->item(0);
      self::assertEquals('test doc', $title);

      $html->remove('//title');

      $title = $html->value('//title')->item(0);
      self::assertEmpty($title);

    }


    public function testDeleteAttribute() {
      $html = $this->getHtmlTestObject();

      $title = $html->value('//a/@title')->getFirst();
      self::assertEquals('my blog', $title);

      $html->remove('//a/@title');

      $title = $html->value('//a/@title')->getFirst();
      self::assertEmpty($title);

    }


    public function testHtmlSelector() {
      $html = $this->getHtmlTestObject();
      $stringCollection = $html->content('//td');

      self::assertCount(1, $stringCollection);
      self::assertEquals('', $stringCollection->item(10));

      $title = $stringCollection->item(0);
      self::assertEquals('custom <a href="http://funivan.com/" title="my blog">link</a>', (string) $title);

      $title = $html->content('//td/@df')->item(0);
      self::assertEmpty((string) $title);
    }


    public function testRegexpReplace() {
      $html = $this->getHtmlDataObject();
      $html->replace('!-!', '+');

      self::assertContains('45+12+16', (string) $html);

      $phones = $html->content('//*[@id="tels"]');

      self::assertCount(1, $phones);

      $phones->replace('![+\s]!');

      self::assertContains('451216', $phones->getFirst());

    }


    public function testMatch() {

      $html = $this->getHtmlDataObject();
      $regex = '!([\d-]+)[<|\n]!';

      $phones = $html->match($regex);
      self::assertCount(2, $phones);

      $phones = $html->match($regex, 0);
      self::assertCount(2, $phones);
      self::assertContains('<', $phones->item(0));
      self::assertContains("\n", $phones->item(1));

      $phones = $html->match($regex, 4);
      self::assertCount(0, $phones);

    }


    public function testMatchWithCallback() {

      $html = $this->getHtmlDataObject();
      $regex = '!([\d-]+)[<|\n]!';

      $phones = $html->match($regex, function (array $items) {
        foreach ((array) $items[1] as $index => $tel) {
          $items[1][$index] = str_replace('-', '', $tel);
        }
        return $items[1];
      });

      self::assertCount(2, $phones);

      self::assertEquals('451216', (string) $phones->item(0));
      self::assertEquals('841890', (string) $phones->item(1));

    }


    /**
     * @expectedException \InvalidArgumentException
     */
    public function testMatchWithInvalidArgument() {

      $html = $this->getHtmlDataObject();
      $html->match('!([\d-]+)[<|\n]!', new \stdClass());
    }


    /**
     * @expectedException \Exception
     */
    public function testMatchWithInvalidCallback() {

      $html = $this->getHtmlDataObject();
      $html->match('!([\d-]+)[<|\n]!', function () {
        return 123;
      });
    }


    /**
     * @expectedException \Exception
     */
    public function testMatchWithInvalidCallbackData() {

      $html = $this->getHtmlDataObject();
      $html->match('!([\d-]+)[<|\n]!', function () {
        return [new \stdClass()];
      });

    }


    public function testMatchWithEmptyElements() {

      $html = $this->getHtmlDataObject();
      $items = $html->match('!(1233)!');
      self::assertEmpty($items);

    }


    public function testObjectWithInnerContent() {

      $html = $this->getHtmlTestObject();

      # inner
      $spanItems = $html->object('//span');
      self::assertCount(4, $spanItems);

      $firstItem = $spanItems->item(0);

      self::assertNotContains('<span class="span-1">', (string) $firstItem);
      self::assertContains('<b>1 </b>', (string) $firstItem);
    }


    public function testReplaceAllData() {
      $html = $this->getHtmlTestObject();
      $html->replace('!.*!');
    }


    /**
     * How would you find all nodes between all H2's?
     *
     * Using Kayessian XPath formula
     *
     * `$ns1[count(.|$ns2) = count($ns2)]`
     *
     * you can select all the nodes that belong both to the node sets $ns1 and $ns2.
     *
     */
    public function testGetAllNodesBetweenSiblings() {
      $html = new ElementFinder('
        <html>
          <h2>Title1</h2>
            <p>Text 1</p>
          <h2>Title2</h2>
            <p>Text 2</p>
          <h2>Title3</h2>
            <p>Text 3</p>
          <h2>Title4</h2>
            <p>Text 4</p>
          <h2>Title5</h2>
        </html>
      ');
      $ns1 = '//*/h2[1]/following-sibling::p';
      $ns2 = '//*/h2[count(//h2)]/preceding-sibling::p';
      $result = $html->value($ns1 . '[count(.|' . $ns2 . ') = count(' . $ns2 . ')]')->getItems();

      self::assertCount(4, $result);
      self::assertEquals($result[0], 'Text 1');
      self::assertEquals($result[3], 'Text 4');

    }


    public function testInitClassWithInvalidContent() {
      $elementFinder = new ElementFinder('
        <!DOCTYPE html>
        <html>
          <head></head>
          <body>
            <span></span></span>
          </body>
        </html>
      ');

      $errors = $elementFinder->getLoadErrors();

      self::assertCount(1, $errors);
      self::assertContains("Unexpected end tag : span\n", $errors[0]->message);

    }


    public function testInitClassWithValidContent() {
      $dataObject = $this->getHtmlDataObject();

      $errors = $dataObject->getLoadErrors();

      self::assertCount(0, $errors);
    }


    public function testGetObjectWithEmptyHtml() {
      $page = new ElementFinder('<div></div><div><a>df</a></div>');
      $objects = $page->object('//div');

      self::assertEmpty((string) $objects->item(0));
      self::assertContains('data-document-is-empty', $objects->item(0)->content('/')->item(0));

      self::assertNotEmpty((string) $objects->item(1));
      $linkText = $objects->item(1)->value('//a')->item(0);
      self::assertEquals('df', $linkText);

    }


    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidDocumentType() {
      new ElementFinder('<div></div>', false);
    }


    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidDocumentOptions() {
      new ElementFinder('<div></div>', null, 'test');
    }


    /**
     *
     */
    public function testValidDocumentType() {
      $document = new ElementFinder('<xml><list>123</list></xml>', ElementFinder::DOCUMENT_XML);
      self::assertContains('<list>123</list>', (string) $document);
    }


    public function testFetchTextNode() {

      $html = new ElementFinder('
        <div>
          <ul>
            <li><b>param1:</b>t1<span>or</span>t2</li>
            <li><b>param2:</b>other</li>
            <li>param3: new</li>
          </ul>
        </div>
      ');


      $firstTextNodes = $html->value('//b/following-sibling::text()[1]')->getItems();

      self::assertEquals([
        't1', 'other',
      ], $firstTextNodes);


      $allFollowingSiblingTextNodes = $html->value('//b/following-sibling::text()')->getItems();

      self::assertEquals([
        't1', 't2', 'other',
      ], $allFollowingSiblingTextNodes);

    }


    public function testKeyValue() {
      $html = new ElementFinder('
        <table>
          <tbody>
          <tr>
            <td>Year</td>
            <td>2016</td>
          </tr>
          <tr>
            <td>Make</td>
            <td>CAT</td>
          </tr>
          <tr>
            <td>Model</td>
            <td>560G</td>
          </tr>
          </tbody>
        </table>
      ');
      $values = $html->keyValue('//table//td[1]', '//table//td[2]');

      self::assertEquals([
        'Year' => '2016',
        'Make' => 'CAT',
        'Model' => '560G',
      ], $values);
    }


    /**
     * @expectedException \Exception
     */
    public function testKeyValueFail() {
      $html = new ElementFinder('
        <table>
          <tbody>
          <tr>
            <td>Year</td>
            <td>2016</td>
          </tr>
          <tr>
            <td>Make</td>
            <td>CAT</td>
          </tr>
          <tr>
            <td>560G</td>
          </tr>
          </tbody>
        </table>
      ');
      $html->keyValue('//table//td[1]', '//table//td[2]');

    }


    public function testXmlData() {
      $xml = new ElementFinder($this->getValidXml(), ElementFinder::DOCUMENT_XML);
      $foods = $xml->object('//food');

      self::assertCount(5, $foods);

      $xml->remove('//food[3]');

      $foods = $xml->object('//food');
      self::assertCount(4, $foods);

      self::assertEquals('$5.95', $xml->value('//food[1]/price/@value')->getFirst());

      self::assertEquals(950, $xml->value('//food/calories')->getLast());

      self::assertEquals(900, $xml->content('//food[2]/calories')->getFirst());

      self::assertEquals('5.95 USD', $xml->match('!<price value="([^"]+)"!iu')->replace('!^\\$(.+)!iu', '$1 USD')->getFirst());

    }


    public function testXmlRootNode() {
      $xml = new ElementFinder($this->getValidXml(), ElementFinder::DOCUMENT_XML);
      $food = $xml->object('//food')->item(2);
      self::assertEquals(900, $food->value('/root/calories')->getFirst());
    }


    public function testLoadXmlWithoutErrors() {
      $xml = new ElementFinder($this->getValidXml(), ElementFinder::DOCUMENT_XML);

      self::assertCount(0, $xml->getLoadErrors());
    }


    public function testLoadXmlWithErrors() {
      $xml = new ElementFinder($this->getInvalidXml(), ElementFinder::DOCUMENT_XML);
      $errors = $xml->getLoadErrors();

      self::assertCount(1, $errors);
      self::assertContains('Opening and ending tag mismatch: from', $errors[0]->message);
    }


    public function testXmlRootNodes() {
      $xml = new ElementFinder($this->getInvalidRootNodesXml(), ElementFinder::DOCUMENT_XML);
      $errors = $xml->getLoadErrors();

      self::assertCount(1, $errors);
      self::assertContains('Extra content at the end of the document', $errors[0]->message);
    }


    /**
     * @return string
     */
    private function getInvalidRootNodesXml() {
      return '<?xml version="1.0" encoding="UTF-8"?>
      <note>
        <to>Tove</to>
        <from>Jani</from>
          <heading>Reminder</heading>
          <body>Don\'t forget me this weekend!</body>
      </note>
      <note>
        <to>John</to>
        <from>Doe</from>
          <heading>Reminder 2</heading>
          <body>Don\'t forget me this month!</body>
      </note>
      ';
    }


    /**
     * @return string
     */
    private function getInvalidXml() {
      return '<?xml version="1.0" encoding="UTF-8"?>
      <note>
        <to>Tove</to>
        <from>Jani</Ffrom>
          <heading>Reminder</heading>
          <body>Don\'t forget me this weekend!</body>
      </note>
      ';
    }


    /**
     * @return string
     */
    private function getValidXml() {
      return '<?xml version="1.0" encoding="UTF-8"?>
      <breakfast_menu>
          <food>
              <name>Belgian Waffles</name>
              <price value="$5.95"/>
              <description>Two of our famous Belgian Waffles with plenty of real maple syrup</description>
              <calories>650</calories>
          </food>
          <food>
              <name>Strawberry Belgian Waffles</name>
              <price value="$7.95"/>
              <description>Light Belgian waffles covered with strawberries and whipped cream</description>
              <calories>900</calories>
          </food>
          <food>
              <name>Berry-Berry Belgian Waffles</name>
              <price value="$8.95"/>
              <description>Light Belgian waffles covered with an assortment of fresh berries and whipped cream</description>
              <calories>900</calories>
          </food>
          <food>
              <name>French Toast</name>
              <price value="$4.50"/>
              <description>Thick slices made from our homemade sourdough bread</description>
              <calories>600</calories>
          </food>
          <food>
              <name>Homestyle Breakfast</name>
              <price value="$6.95"/>
              <description>Two eggs, bacon or sausage, toast, and our ever-popular hash browns</description>
              <calories>950</calories>
          </food>
      </breakfast_menu>
      ';
    }


    public function testShareExpressionTranslator() {
      $page = new ElementFinder('
          <div class="node"> 
            <a href="#" class="link">test0</a>
          </div>
          <div class="node"> 
            <a href="#" class="link">test1</a>
          </div>
          <div class="node"> 
            <a href="#" class="link">test2</a>
          </div>
');

      $page->setExpressionTranslator(new ItemsByClassExpressionTranslator());

      $expression = 'node';

      $objects = $page->object($expression);
      self::assertCount(3, $objects);

      foreach ($objects as $index => $object) {
        self::assertNotNull($object->getExpressionTranslator());
        $link = $object->content('link');
        self::assertCount(1, $link);
        self::assertEquals('test' . $index, $link->getFirst());
      }

    }


    /**
     * @return string
     */
    protected function getDemoDataDirectoryPath() {
      return __DIR__ . '/demo-data/';
    }


    /**
     * @return \Xparse\ElementFinder\ElementFinder
     */
    public function getHtmlTestObject() {
      return $this->initFromFile('test.html');
    }


    /**
     * @return \Xparse\ElementFinder\ElementFinder
     */
    public function getHtmlDataObject() {
      return $this->initFromFile('data.html');
    }


    /**
     * @return \Xparse\ElementFinder\ElementFinder
     */
    public function getNodeItemsHtmlObject() {
      return $this->initFromFile('node-items.html');
    }


    /**
     * @param string $file
     * @return \Xparse\ElementFinder\ElementFinder
     */
    protected function initFromFile($file) {
      $fileData = file_get_contents($this->getDemoDataDirectoryPath() . DIRECTORY_SEPARATOR . $file);
      return new \Xparse\ElementFinder\ElementFinder($fileData);
    }

  }