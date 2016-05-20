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
      new \Xparse\ElementFinder\ElementFinder("", "df");
    }


    /**
     * @expectedException \InvalidArgumentException
     */
    public function testLoadEmptyDoc() {
      new \Xparse\ElementFinder\ElementFinder("");
    }


    /**
     *
     */
    public function testLoadDocumentWithZero() {
      $elementFinder = new \Xparse\ElementFinder\ElementFinder("   0 ");
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

      /** @var \Xparse\ElementFinder\ElementFinder $span */
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
      $elementFinder = new \Xparse\ElementFinder\ElementFinder('
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
      $page = new \Xparse\ElementFinder\ElementFinder("<div></div><div><a>df</a></div>");
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
      new \Xparse\ElementFinder\ElementFinder("<div></div>", false);
    }


    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidDocumentOptions() {
      new \Xparse\ElementFinder\ElementFinder("<div></div>", null, 'test');
    }


    /**
     *
     */
    public function testValidDocumentType() {
      $document = new \Xparse\ElementFinder\ElementFinder("<xml><list>123</list></xml>", ElementFinder::DOCUMENT_XML);
      $this->assertContains('<list>123</list>', (string) $document);
    }


    public function testFetchTextNode() {

      $html = new \Xparse\ElementFinder\ElementFinder('
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
      $html = new \Xparse\ElementFinder\ElementFinder('
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
      $html = new \Xparse\ElementFinder\ElementFinder('
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


    public function testInvalidExpression() {
      $html = $this->getHtmlTestObject();
      try {
        $html->value('b://or:other')->getFirst();
      } catch (\Exception $e) {
        $this->assertContains('Invalid expression', $e->getMessage());
      }
    }

  }