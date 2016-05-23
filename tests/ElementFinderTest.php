<?php

  namespace Test\Xparse\ElementFinder;

  use Xparse\ElementFinder\ElementFinder;

  /**
   *
   * @package Test\Xparse\ElementFinder
   */
  class ElementFinderTest extends \Test\Xparse\ElementFinder\Main {

    public function testLoad() {
      $html = $this->getHtmlTestObject();
      $this->assertContains('<title>test doc</title>', (string) $html);
    }


    /**
     * @expectedException \Exception
     */
    public function testInvalidType() {
      new ElementFinder("", "df");
    }


    /**
     * @expectedException \InvalidArgumentException
     */
    public function testLoadEmptyDoc() {
      new ElementFinder("");
    }


    /**
     *
     */
    public function testLoadDocumentWithZero() {
      $elementFinder = new ElementFinder("   0 ");
      $this->assertContains('0', (string) $elementFinder);
    }


    public function testNodeList() {

      $html = $this->getHtmlTestObject();
      $spanNodes = $html->node('//span');

      $this->assertInstanceOf('\DOMNodeList', $spanNodes);

      $this->assertEquals(4, $spanNodes->length);
    }


    public function testAttributes() {
      $html = $this->getHtmlTestObject();

      $links = $html->value("//a/@href");

      $this->assertCount(1, $links);

      foreach ($html->html("//a") as $htmlString) {
        $this->assertTrue(is_string($htmlString));
      }

      $firstLink = $html->html("//a", true)->item(0);

      $this->assertContains('<a href="http://funivan.com/" title="my blog">link</a>', (string) $firstLink);
    }


    public function testObjects() {
      $html = $this->getHtmlTestObject();

      $spanItems = $html->object("//span");

      $this->assertCount(4, $spanItems);

      /** @var ElementFinder $span */
      foreach ($spanItems->extractItems(0, 3) as $index => $span) {
        $itemHtml = $span->html('//i')->item(0);

        $this->assertEquals('r', trim($itemHtml));

      }

      $html->remove('//span[2]');

      $spanItems = $html->html("//span");
      $this->assertCount(3, $spanItems);

      $html->remove('//span[@class]');

      $spanItems = $html->html("//span");
      $this->assertCount(1, $spanItems);

    }


    public function testDeleteNode() {
      $html = $this->getHtmlTestObject();

      $title = $html->value('//title')->item(0);
      $this->assertEquals('test doc', $title);

      $html->remove('//title');

      $title = $html->value('//title')->item(0);
      $this->assertEmpty($title);

    }


    public function testDeleteAttribute() {
      $html = $this->getHtmlTestObject();

      $title = $html->value('//a/@title')->getFirst();
      $this->assertEquals('my blog', $title);

      $html->remove('//a/@title');

      $title = $html->value('//a/@title')->getFirst();
      $this->assertEmpty($title);

    }


    public function testHtmlSelector() {
      $html = $this->getHtmlTestObject();
      $stringCollection = $html->html('//td');

      $this->assertCount(1, $stringCollection);
      $this->assertEquals('', $stringCollection->item(10));

      $title = $stringCollection->item(0);
      $this->assertEquals('custom <a href="http://funivan.com/" title="my blog">link</a>', (string) $title);

      $title = $html->html('//td/@df')->item(0);
      $this->assertEmpty((string) $title);
    }


    public function testGetNodeItems() {
      $html = $this->getHtmlTestObject();
      $group = $html->getNodeItems('//span', [
        'b' => '//b[1]',
        'i' => '//o',
        'if' => '//i/@df',
      ]);

      $this->assertCount(4, $group);

      $this->assertNotEmpty($group[0]['b']);

      foreach ($group as $i => $item) {
        $this->assertEmpty($item['if']);
      }

    }


    public function testRegexpReplace() {
      $html = $this->getHtmlDataObject();
      $html->replace('!-!', '+');

      $this->assertContains('45+12+16', (string) $html);

      $phones = $html->html('//*[@id="tels"]');

      $this->assertCount(1, $phones);

      $phones->replace('![\+\s]!');

      $this->assertContains('451216', $phones->getFirst());

    }


    public function testMatch() {

      $html = $this->getHtmlDataObject();
      $regex = '!([\d-]+)[<|\n]!';

      $phones = $html->match($regex);
      $this->assertCount(2, $phones);

      $phones = $html->match($regex, 0);
      $this->assertCount(2, $phones);
      $this->assertContains('<', $phones[0]);
      $this->assertContains("\n", $phones[1]);

      $phones = $html->match($regex, 4);
      $this->assertCount(0, $phones);

    }


    public function testMatchWithCallback() {

      $html = $this->getHtmlDataObject();
      $regex = '!([\d-]+)[<|\n]!';

      $phones = $html->match($regex, function ($items) {
        foreach ($items[1] as $index => $tel) {
          $items[1][$index] = str_replace('-', '', $tel);
        }
        return $items[1];
      });

      $this->assertCount(2, $phones);

      $this->assertEquals('451216', (string) $phones[0]);
      $this->assertEquals('841890', (string) $phones[1]);

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
      $this->assertEmpty($items);

    }


    public function testObjectWithInnerHtml() {

      $html = $this->getHtmlTestObject();

      # inner
      $spanItems = $html->object('//span');
      $this->assertCount(4, $spanItems);

      $firstItem = $spanItems->item(0);

      $this->assertNotContains('<span class="span-1">', (string) $firstItem);
      $this->assertContains('<b>1 </b>', (string) $firstItem);
    }


    public function testReplaceAllData() {
      $html = $this->getHtmlTestObject();
      $html->replace('!.*!');
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

      $this->assertCount(1, $errors);
      $this->assertContains("Unexpected end tag : span\n", $errors[0]->message);

    }


    public function testInitClassWithValidContent() {
      $dataObject = $this->getHtmlDataObject();

      $errors = $dataObject->getLoadErrors();

      $this->assertCount(0, $errors);
    }


    public function testGetObjectWithEmptyHtml() {
      $page = new ElementFinder("<div></div><div><a>df</a></div>");
      $objects = $page->object('//div');

      $this->assertEmpty((string) $objects->item(0));
      $this->assertContains('data-document-is-empty', $objects[0]->html('/')->item(0));

      $this->assertNotEmpty((string) $objects->item(1));
      $linkText = $objects->item(1)->value('//a')->item(0);
      $this->assertEquals('df', $linkText);

    }


    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidDocumentType() {
      new ElementFinder("<div></div>", false);
    }


    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidDocumentOptions() {
      new ElementFinder("<div></div>", null, 'test');
    }


    /**
     *
     */
    public function testValidDocumentType() {
      $document = new ElementFinder("<xml><list>123</list></xml>", ElementFinder::DOCUMENT_XML);
      $this->assertContains('<list>123</list>', (string) $document);
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

      $this->assertEquals([
        't1', 'other',
      ], $firstTextNodes);


      $allFollowingSiblingTextNodes = $html->value('//b/following-sibling::text()')->getItems();

      $this->assertEquals([
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
      $values = $html->keyValue("//table//td[1]", "//table//td[2]");

      $this->assertEquals([
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
      $html->keyValue("//table//td[1]", "//table//td[2]");

    }


    public function testXmlData() {
      $xml = new ElementFinder($this->getValidXml(), ElementFinder::DOCUMENT_XML);
      $foods = $xml->object('//food');

      $this->assertCount(5, $foods);

      $xml->remove('//food[3]');

      $foods = $xml->object('//food');
      $this->assertCount(4, $foods);

      $this->assertEquals('$5.95', $xml->value("//food[1]/price/@value")->getFirst());

      $this->assertEquals(950, $xml->value('//food/calories')->getLast());

      $this->assertEquals(900, $xml->html('//food[2]/calories')->getFirst());

      $this->assertEquals('5.95 USD', $xml->match('!<price value="([^"]+)"!iu')->replace('!^\\$(.+)!iu', '$1 USD')->getFirst());

    }


    public function testLoadXmlWithoutErrors() {
      $xml = new ElementFinder($this->getValidXml(), ElementFinder::DOCUMENT_XML);

      $this->assertCount(0, $xml->getLoadErrors());
    }


    public function testLoadXmlWithErrors() {
      $xml = new ElementFinder($this->getInvalidXml(), ElementFinder::DOCUMENT_XML);
      $errors = $xml->getLoadErrors();

      $this->assertCount(1, $errors);
      $this->assertContains("Opening and ending tag mismatch: from", $errors[0]->message);
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
  }